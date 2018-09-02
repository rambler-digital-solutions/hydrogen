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
 * Class Group
 */
class Group extends Criterion
{
    /**
     * @var Query
     */
    private $parent;
    /**
     * @var bool
     */
    private $conjunction;
    /**
     * @var \Closure
     */
    private $then;

    /**
     * Group constructor.
     * @param Query $parent
     * @param \Closure $then
     * @param bool $conjunction
     */
    public function __construct(Query $parent, \Closure $then, bool $conjunction = true)
    {
        $this->then = $then;
        $this->parent = $parent;
        $this->conjunction = $conjunction;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        throw new \LogicException(\sprintf('Criterion %s does not provide the field', \class_basename($this)));
    }

    /**
     * @return bool
     */
    public function isAnd(): bool
    {
        return $this->conjunction;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        $query = $this->parent->attach($this->parent->create());

        ($this->then)($query);

        foreach ($query->getCriteria() as $criterion) {
            if (! $criterion instanceof Where) {
                $error = 'Groups allow to specify only Where selections, but %s given';
                throw new \LogicException(\sprintf($error, $criterion));
            }
        }

        return $query;
    }
}
