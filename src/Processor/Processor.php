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
use RDS\Hydrogen\Criteria\Common\Field;
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
     * @var array|BuilderInterface[]
     */
    private $builderInstances = [];

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
     * @param mixed $context
     * @param Query $query
     * @return \Generator
     */
    protected function forEach($context, Query $query): \Generator
    {
        foreach ($query->getCriteria() as $criterion) {
            $builder = $this->getBuilder($criterion);

            yield from $builder->apply($context, $criterion);
        }
    }

    /**
     * @param CriterionInterface $criterion
     * @return BuilderInterface
     */
    protected function getBuilder(CriterionInterface $criterion): BuilderInterface
    {
        $key = \get_class($criterion);

        if (isset($this->builderInstances[$key])) {
            return $this->builderInstances[$key];
        }

        $processor = static::CRITERIA_MAPPINGS[\get_class($criterion)] ?? null;

        if ($processor === null) {
            $error = \vsprintf('%s processor does not support the "%s" criterion', [
                \str_replace_last('Processor', '', \class_basename($this)),
                \class_basename($criterion),
            ]);

            throw new \InvalidArgumentException($error);
        }

        return $this->builderInstances[$key] = new $processor($this->meta, $this->em);
    }

    /**
     * @param \Generator $generator
     * @param Query $query
     * @param \Closure $each
     * @return \Generator
     */
    protected function bypass(\Generator $generator, Query $query, \Closure $each): \Generator
    {
        $this->builderInstances = [];

        while ($generator->valid()) {
            [$key, $value] = [$generator->key(), $generator->current()];

            yield from $result = $this->yieldThrough($key, $value, $query, $each);

            $response = $result->getReturn();

            \assert($response !== null);

            $generator->send($response);
        }
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @param Query $query
     * @param \Closure $each
     * @return \Generator
     */
    private function yieldThrough($key, $value, Query $query, \Closure $each): \Generator
    {
        switch (true) {
            case $value instanceof Field:
                return $this->applyField($query, $value);
                break;

            case $key instanceof Field:
                yield from $co = $this->applyFieldWithValue($query, $key, $value);
                return $co->getReturn();
                break;

            case $value instanceof \Closure:
                yield $value;
                return $value;

            case $value instanceof Query:
                $query->merge($value);
                return $query;

            default:
                return $each($value, $key);
        }
    }

    /**
     * @param Query $query
     * @param Field $field
     * @return string
     */
    private function applyField(Query $query, Field $field): string
    {
        return $field->withQuery($query)->toString();
    }

    /**
     * @param Query $query
     * @param Field $field
     * @param $value
     * @return \Generator
     * @throws \Exception
     */
    private function applyFieldWithValue(Query $query, Field $field, $value): \Generator
    {
        $placeholder = $query->placeholder($field->getName());

        yield $placeholder => $value;

        return $placeholder;
    }
}
