<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\HavingGroup;
use RDS\Hydrogen\Criteria\WhereGroup;
use RDS\Hydrogen\Criteria\GroupBy;
use RDS\Hydrogen\Criteria\Having;
use RDS\Hydrogen\Query;

/**
 * Trait GroupByProvider
 * @mixin Query
 */
trait GroupByProvider
{
    /**
     * @param string[] $fields
     * @return Query|$this|self
     */
    public function groupBy(string ...$fields): self
    {
        foreach ($fields as $field) {
            $this->add(new GroupBy($this, $field));
        }

        return $this;
    }

    /**
     * @param string|\Closure $field
     * @param $valueOrOperator
     * @param null $value
     * @return Query|$this|self
     */
    public function orHaving($field, $valueOrOperator = null, $value = null): self
    {
        return $this->or->having($field, $valueOrOperator, $value);
    }

    /**
     * @param string|\Closure $field
     * @param $valueOrOperator
     * @param null $value
     * @return Query|$this|self
     */
    public function having($field, $valueOrOperator = null, $value = null): self
    {
        if (\is_string($field)) {
            [$operator, $value] = Having::completeMissingParameters($valueOrOperator, $value);

            return $this->add(new Having($this, $field, $operator, $value, $this->mode()));
        }

        if ($field instanceof \Closure) {
            return $this->add(new HavingGroup($this, $field, $this->mode()));
        }

        $error = \vsprintf('Selection set should be a type of string or Closure, but %s given', [
            \studly_case(\gettype($field)),
        ]);

        throw new \InvalidArgumentException($error);
    }
}
