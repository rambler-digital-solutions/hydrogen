<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria;
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Query;

/**
 * Class DatabaseProcessor
 */
class DatabaseProcessor extends Processor
{
    /**
     * @var string[]|BuilderInterface[]
     */
    protected const CRITERIA_MAPPINGS = [
        Criteria\GroupBy::class     => DatabaseProcessor\GroupByBuilder::class,
        Criteria\Having::class      => DatabaseProcessor\HavingBuilder::class,
        Criteria\HavingGroup::class => DatabaseProcessor\HavingGroupBuilder::class,
        Criteria\Join::class        => DatabaseProcessor\JoinBuilder::class,
        Criteria\Limit::class       => DatabaseProcessor\LimitBuilder::class,
        Criteria\Offset::class      => DatabaseProcessor\OffsetBuilder::class,
        Criteria\OrderBy::class     => DatabaseProcessor\OrderByBuilder::class,
        Criteria\Relation::class    => DatabaseProcessor\RelationBuilder::class,
        Criteria\Selection::class   => DatabaseProcessor\SelectBuilder::class,
        Criteria\Where::class       => DatabaseProcessor\WhereBuilder::class,
        Criteria\WhereGroup::class  => DatabaseProcessor\GroupBuilder::class,
    ];

    /**
     * @param Query $query
     * @param string $field
     * @return mixed
     */
    public function getScalarResult(Query $query, string $field)
    {
        $query->from($this->repository);

        /** @var QueryBuilder $builder */
        [$deferred, $builder] = $this->await($this->createQueryBuilder($query));

        return $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Query $query
     * @return string
     */
    public function dump(Query $query): string
    {
        $query->from($this->repository);

        /** @var QueryBuilder $builder */
        [$deferred, $builder] = $this->await($this->createQueryBuilder($query));

        return $builder->getQuery()->getDQL();
    }

    /**
     * @param Query $query
     * @return \Generator
     */
    protected function createQueryBuilder(Query $query): \Generator
    {
        $builder = $this->em->createQueryBuilder();
        $builder->from($query->getRepository()->getClassName(), $query->getAlias());
        $builder->setCacheable(false);

        return $this->fillQueryBuilder($builder, $query);
    }

    /**
     * @param QueryBuilder $builder
     * @param Query $query
     * @return \Generator
     */
    protected function fillQueryBuilder(QueryBuilder $builder, Query $query): \Generator
    {
        /**
         * @var \Generator $context
         * @var CriterionInterface $criterion
         */
        foreach ($this->bypass($builder, $query) as $criterion => $context) {
            while ($context->valid()) {
                [$key, $value] = [$context->key(), $context->current()];

                switch (true) {
                    case $key instanceof Field:
                        $context->send($placeholder = $query->createPlaceholder($key->toString()));
                        $builder->setParameter($placeholder, $value);
                        continue 2;

                    case $value instanceof Field:
                        $context->send($value->toString($criterion->getQueryAlias()));
                        continue 2;

                    case $value instanceof Query:
                        $context->send($query->attach($value));
                        continue 2;

                    default:
                        $result = (yield $key => $value);

                        if ($result === null) {
                            $stmt = \is_object($value) ? \get_class($value) : \gettype($value);
                            $error = 'Unrecognized coroutine\'s return statement: ' . $stmt;
                            $context->throw(new \InvalidArgumentException($error));
                        }

                        $context->send($result);
                }
            }
        }

        return $builder;
    }

    /**
     * @param Query $query
     * @param string ...$fields
     * @return iterable
     */
    public function getResult(Query $query, string ...$fields): iterable
    {
        $query->from($this->repository);

        if (! $query->has(Criteria\Selection::class)) {
            $query->select(':' . $query->getAlias());
        }

        /**
         * @var QueryBuilder $builder
         * @var Queue $deferred
         */
        [$deferred, $builder] = $this->await($this->createQueryBuilder($query));

        return \count($fields) > 0
            ? $this->executeFetchFields($builder, $fields)
            : $this->executeFetchData($builder, $deferred);
    }

    /**
     * @param QueryBuilder $builder
     * @param array $fields
     * @return array
     */
    private function executeFetchFields(QueryBuilder $builder, array $fields): array
    {
        $result = [];

        foreach ($builder->getQuery()->getArrayResult() as $record) {
            $result[] = \array_merge(\array_only($record, $fields), \array_only($record[0] ?? [], $fields));
        }

        return $result;
    }

    /**
     * @param QueryBuilder $builder
     * @param Queue $deferred
     * @return array
     */
    private function executeFetchData(QueryBuilder $builder, Queue $deferred): array
    {
        $query = $builder->getQuery();

        $deferred->invoke($result = $query->getResult());

        return $result;
    }
}
