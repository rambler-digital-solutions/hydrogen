<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Query;

/**
 * Class Processor
 */
abstract class Processor implements ProcessorInterface
{
    /**
     * @var string[]|BuilderInterface[]
     */
    protected const CRITERIA_MAPPINGS = [];

    /**
     * @var ObjectRepository|EntityRepository
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ClassMetadata
     */
    protected $meta;

    /**
     * DatabaseProcessor constructor.
     * @param ObjectRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(ObjectRepository $repository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->meta = $em->getClassMetadata($repository->getClassName());
        $this->repository = $repository;

        \assert(\count(static::CRITERIA_MAPPINGS));
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return EntityRepository|ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetadata(): ClassMetadata
    {
        return $this->meta;
    }

    /**
     * @param mixed $context
     * @param Query $query
     * @return \Generator
     */
    protected function bypass($context, Query $query): \Generator
    {
        foreach ($this->builders($query) as $criterion => $builder) {
            $result = $builder->apply($context, $criterion);

            if (\is_iterable($result)) {
                yield $criterion => $result;
            }
        }
    }

    /**
     * @param \Generator $generator
     * @return array
     */
    protected function await(\Generator $generator): array
    {
        $queue = new Queue();

        while ($generator->valid()) {
            $value = $generator->current();

            if ($value instanceof \Closure) {
                $queue->push($value);
            }

            $generator->next();
        }

        return [$queue, $generator->getReturn()];
    }

    /**
     * @param Query $query
     * @return \Generator|BuilderInterface[]
     */
    private function builders(Query $query): \Generator
    {
        $context = [];

        foreach ($query->getCriteria() as $criterion) {
            $key = \get_class($criterion);

            yield $criterion => $context[$key] ?? $context[$key] = $this->getBuilder($query, $criterion);
        }

        unset($context);
    }

    /**
     * @param Query $query
     * @param CriterionInterface $criterion
     * @return BuilderInterface
     */
    protected function getBuilder(Query $query, CriterionInterface $criterion): BuilderInterface
    {
        $processor = static::CRITERIA_MAPPINGS[\get_class($criterion)] ?? null;

        if ($processor === null) {
            $error = \vsprintf('%s processor does not support the "%s" criterion', [
                \str_replace_last('Processor', '', \class_basename($this)),
                \class_basename($criterion),
            ]);

            throw new \InvalidArgumentException($error);
        }

        return new $processor($query, $this);
    }

    /**
     * @param string $entity
     * @return ProcessorInterface
     */
    public function getProcessor(string $entity): ProcessorInterface
    {
        $repository = $this->em->getRepository($entity);

        if (\method_exists($repository, 'getProcessor')) {
            return $repository->getProcessor();
        }

        return new static($repository, $this->em);
    }
}
