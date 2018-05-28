<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Str;
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Group;
use RDS\Hydrogen\Criteria\GroupBy;
use RDS\Hydrogen\Criteria\Limit;
use RDS\Hydrogen\Criteria\Offset;
use RDS\Hydrogen\Criteria\Relation;
use RDS\Hydrogen\Criteria\Selection;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Processor\DatabaseProcessor\DatabaseCriterionProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\GroupByProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\GroupProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\LimitProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\OffsetProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\RelationProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\SelectProcessor;
use RDS\Hydrogen\Processor\DatabaseProcessor\WhereProcessor;
use RDS\Hydrogen\Query;

/**
 * Class DatabaseProcessor
 */
class DatabaseProcessor implements ProcessorInterface
{
    /**
     * @var array|DatabaseCriterionProcessor[]
     */
    private const MAPPINGS = [
        Where::class     => WhereProcessor::class,
        Limit::class     => LimitProcessor::class,
        Offset::class    => OffsetProcessor::class,
        GroupBy::class   => GroupByProcessor::class,
        Selection::class => SelectProcessor::class,
        Group::class     => GroupProcessor::class,
        Relation::class  => RelationProcessor::class,
    ];

    /**
     * @var int
     */
    private static $lastSelectionId = 0;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ClassMetadata
     */
    private $meta;

    /**
     * @var string
     */
    private $alias;

    /**
     * DatabaseProcessor constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em    = $em;
        $this->meta  = $meta;
        $this->alias = $this->createAlias($meta);
    }

    /**
     * @param ClassMetadata $meta
     * @return string
     */
    private function createAlias(ClassMetadata $meta): string
    {
        return \vsprintf('%s_%s', [
            Str::snake(\class_basename($meta->getName())),
            ++self::$lastSelectionId,
        ]);
    }

    /**
     * @param Query $query
     * @return iterable|object[]
     */
    public function get(Query $query): iterable
    {
        return $this->toBuilder($query)->getQuery()->getResult();
    }

    /**
     * @param Query $query
     * @return QueryBuilder
     */
    public function toBuilder(Query $query): QueryBuilder
    {
        $builder = $this->em->createQueryBuilder()
            ->select($this->alias)
            ->from($this->meta->getName(), $this->alias);

        $this->apply($builder, $query, $this->alias);

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param Query $query
     * @param string $alias
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, Query $query, string $alias): QueryBuilder
    {
        /** @var DatabaseCriterionProcessor[] $criteria */
        $criteria = [];

        foreach ($query->getCriteria() as $criterion) {
            $identifier = \get_class($criterion);

            if (! \array_key_exists($identifier, $criteria)) {
                $criteria[$identifier] = $this->criterion($criterion, $alias);
            }

            /** @var DatabaseCriterionProcessor $applicator */
            $applicator = $criteria[$identifier];

            $builder = $applicator->apply($builder, $criterion);

            foreach ($applicator->getParameters() as $field => $value) {
                $builder->setParameter($field, $value);
            }
        }

        return $builder;
    }

    /**
     * @param CriterionInterface $criterion
     * @param string $alias
     * @return DatabaseCriterionProcessor
     */
    private function criterion(CriterionInterface $criterion, string $alias): DatabaseCriterionProcessor
    {
        $processor = self::MAPPINGS[\get_class($criterion)] ?? null;

        if ($processor === null) {
            $error = \vsprintf('%s is not support the %s criterion', [
                \class_basename($this),
                \class_basename($criterion),
            ]);

            throw new \InvalidArgumentException($error);
        }

        return new $processor($alias, $this);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param string|null $entity
     * @return ClassMetadata
     */
    public function getClassMetadata(string $entity = null): ClassMetadata
    {
        return $this->em->getClassMetadata($entity ?? $this->meta->getName());
    }

    /**
     * @param Query $query
     * @return mixed|null|object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function first(Query $query)
    {
        try {
            $builder = $this->toBuilder($query);
            $builder->setMaxResults(1);

            return $builder->getQuery()->getOneOrNullResult();
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * @param Query $query
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function count(Query $query): int
    {
        return $this->scalar($query, function (QueryBuilder $builder, string $field) {
            return $builder->expr()->count($field);
        });
    }

    /**
     * @param Query $query
     * @param \Closure $expr
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function scalar(Query $query, \Closure $expr): int
    {
        $builder = $this->toBuilder($query);

        try {
            return (int)$builder->select($expr($builder, $this->getPrimary()->withAlias($this->alias)))
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | \InvalidArgumentException $e) {
            return 0;
        }
    }

    /**
     * @return Field
     */
    private function getPrimary(): Field
    {
        return new Field(\array_first($this->meta->getIdentifierFieldNames()));
    }
}
