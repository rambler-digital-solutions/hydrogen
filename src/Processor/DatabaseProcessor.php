<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor;

use Doctrine\ORM\Query as DoctrineQuery;
use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria;
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
        Criteria\Group::class     => DatabaseProcessor\GroupBuilder::class,
        Criteria\GroupBy::class   => DatabaseProcessor\GroupByBuilder::class,
        Criteria\Limit::class     => DatabaseProcessor\LimitBuilder::class,
        Criteria\Offset::class    => DatabaseProcessor\OffsetBuilder::class,
        Criteria\OrderBy::class   => DatabaseProcessor\OrderByBuilder::class,
        Criteria\Relation::class  => DatabaseProcessor\RelationBuilder::class,
        Criteria\Selection::class => DatabaseProcessor\SelectBuilder::class,
        Criteria\Where::class     => DatabaseProcessor\WhereBuilder::class,
    ];

    /**
     * @param Query $query
     * @return mixed
     */
    public function getScalarResult(Query $query)
    {
        return $this->exec($query, function (DoctrineQuery $query) {
            return $query->getSingleScalarResult();
        });
    }

    /**
     * @param Query $query
     * @param \Closure $execute
     * @return mixed
     */
    private function exec(Query $query, \Closure $execute)
    {
        $metadata = $this->em->getClassMetadata($this->repository->getClassName());

        $queue = new Queue();

        /** @var QueryBuilder $builder */
        $builder = $this->fillQueueThrough($queue, $this->toBuilder($query));

        $result = $execute($builder->getQuery());

        foreach ($queue->reduce($result, $metadata) as $out) {
            $children = $this->bypass($out, $query, $this->applicator($builder));

            $this->fillQueueThrough($queue, $children);
        }

        return $result;
    }

    /**
     * @param Queue $queue
     * @param \Generator $generator
     * @return QueryBuilder|mixed
     */
    private function fillQueueThrough(Queue $queue, \Generator $generator)
    {
        foreach ($generator as $result) {
            if ($result instanceof \Closure) {
                $queue->push($result);
            }
        }

        return $generator->getReturn();
    }

    /**
     * A set of coroutine operations valid for the current DatabaseProcessor.
     *
     * @param QueryBuilder $builder
     * @return \Closure
     */
    private function applicator(QueryBuilder $builder): \Closure
    {
        return function ($response) use ($builder) {
            // Send the context (the builder) in case the
            // answer contains an empty value.
            if ($response === null) {
                return $builder;
            }

            // In the case that the response is returned to the Query
            // instance - we need to fulfill this query and return a response.
            if ($response instanceof Query) {
                /** @var DatabaseProcessor $processor */
                $processor = $response->getRepository()->getProcessor();

                return $processor->getResult($response);
            }

            return $response;
        };
    }

    /**
     * Applies all necessary operations to the QueryBuilder.
     * Returns a set of pending operations (Deferred) and QueryBuilder.
     *
     * @param QueryBuilder $builder
     * @param Query $query
     * @return \Generator
     */
    protected function apply(QueryBuilder $builder, Query $query): \Generator
    {
        // Add an alias and indicate that this
        // alias is relevant to the entity of a repository.
        $builder->addSelect($query->getAlias());
        $builder->from($query->getRepository()->getClassName(), $query->getAlias());

        //
        //
        $response = $this->bypass($this->forEach($builder, $query), $query, $this->applicator($builder));

        foreach ($response as $field => $value) {
            if ($value instanceof \Closure) {
                yield $value;
                continue;
            }

            $builder->setParameter($field, $value);
        }

        return $builder;
    }

    /**
     * Creates a new QueryBuilder and applies all necessary operations.
     * Returns a set of pending operations (Deferred) and QueryBuilder.
     *
     * @param Query $query
     * @return \Generator|\Closure[]|QueryBuilder
     */
    protected function toBuilder(Query $query): \Generator
    {
        yield from $generator = $this->apply($this->em->createQueryBuilder(), $query);

        return $generator->getReturn();
    }

    /**
     * @param Query $query
     * @return iterable
     */
    public function getResult(Query $query): iterable
    {
        return $this->exec($query, function (DoctrineQuery $query) {
            return $query->getResult();
        });
    }

    /**
     * @param Query $query
     * @return array
     */
    public function getArrayResult(Query $query): array
    {
        return $this->exec($query, function (DoctrineQuery $query) {
            return $query->getArrayResult();
        });
    }
}
