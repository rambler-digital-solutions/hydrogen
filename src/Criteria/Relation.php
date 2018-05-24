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
 * Class Relation
 */
class Relation extends Criterion
{
    /**
     * @var \Closure
     */
    private $inner;

    /**
     * @var Query
     */
    private $parent;

    /**
     * Relation constructor.
     * @param string $relation
     * @param Query $parent
     * @param \Closure|null $inner
     */
    public function __construct(string $relation, Query $parent, \Closure $inner = null)
    {
        $this->inner = $inner;
        $this->parent = $parent;

        parent::__construct($relation);
    }

    /**
     * @return Field
     */
    public function getRelation(): Field
    {
        return $this->field;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        $query = $this->parent->sub();

        if ($this->inner) {
            ($this->inner)($query);
        }

        return $query;
    }
}
