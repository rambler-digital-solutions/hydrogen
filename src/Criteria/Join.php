<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Processor\ProcessorInterface;
use RDS\Hydrogen\Query;

/**
 * Class Join
 */
class Join extends Criterion
{
    public const TYPE_JOIN = 0x01;
    public const TYPE_INNER_JOIN = 0x02;
    public const TYPE_LEFT_JOIN = 0x03;

    /**
     * @var Field
     */
    private $relation;

    /**
     * @var int
     */
    private $type;

    /**
     * @var \Closure|null
     */
    private $inner;

    /**
     * Relation constructor.
     * @param string $relation
     * @param Query $parent
     * @param int $type
     * @param \Closure|null $inner
     */
    public function __construct(Query $parent, string $relation, int $type = self::TYPE_JOIN, \Closure $inner = null)
    {
        parent::__construct($parent);

        $this->relation = $this->field($relation);
        $this->inner = $inner;
        $this->type = $type;
    }

    /**
     * @return Field
     */
    public function getRelation(): Field
    {
        return $this->relation;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function hasJoinQuery(): bool
    {
        return $this->inner !== null;
    }

    /**
     * @param Query|null $query
     * @return Query
     */
    public function getJoinQuery(Query $query = null): Query
    {
        $related = $query ?? $this->query->create();

        if ($this->inner) {
            ($this->inner)($related);
        }

        return $related;
    }

    /**
     * @param ProcessorInterface $processor
     * @return \Generator|array
     */
    public function getRelations(ProcessorInterface $processor): \Generator
    {
        $parent = $processor->getMetadata();

        foreach ($this->getRelation() as $isLast => $relation) {
            yield $isLast => $from = $parent->associationMappings[$relation->getName()];

            $parent = $processor->getProcessor($from['targetEntity'])->getMetadata();
        }
    }
}
