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
     * @var Field
     */
    private $relation;

    /**
     * @var \Closure|null
     */
    private $inner;

    /**
     * Relation constructor.
     * @param string $relation
     * @param Query $parent
     * @param \Closure|null $inner
     */
    public function __construct(Query $parent, string $relation, \Closure $inner = null)
    {
        parent::__construct($parent);

        $this->relation = $this->field($relation);
        $this->inner = $inner;
    }

    /**
     * @return Field
     */
    public function getRelation(): Field
    {
        return $this->relation;
    }

    /**
     * @return Query
     */
    public function getRelatedQuery(): Query
    {
        $related = $this->query->create();

        if ($this->inner) {
            ($this->inner)($related);
        }

        return $related;
    }
}
