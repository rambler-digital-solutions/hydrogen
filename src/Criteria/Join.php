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
     * @var \Closure
     */
    private $inner;

    /**
     * @var Query
     */
    private $parent;

    /**
     * @var int
     */
    private $type;

    /**
     * Relation constructor.
     * @param string $relation
     * @param Query $parent
     * @param int $type
     * @param \Closure|null $inner
     */
    public function __construct(string $relation, Query $parent, int $type = self::TYPE_JOIN, \Closure $inner = null)
    {
        parent::__construct($relation);

        $this->type = $type;
        $this->inner = $inner;
        $this->parent = $parent;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->parent->getAlias();
    }

    /**
     * @return Field
     */
    public function getRelation(): Field
    {
        return $this->getField();
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        $query = $this->parent->create();

        if ($this->inner) {
            ($this->inner)($query);
        }

        return $query;
    }
}
