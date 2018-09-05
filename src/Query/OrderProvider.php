<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\OrderBy;
use RDS\Hydrogen\Criteria\Where\Operator;
use RDS\Hydrogen\Query;

/**
 * Trait OrderProvider
 * @mixin Query
 */
trait OrderProvider
{
    /**
     * @param string $field
     * @param mixed $value
     * @param bool $including
     * @return Query|$this|self
     */
    public function after(string $field, $value, bool $including = false): self
    {
        $operator = $including ? Operator::GT : Operator::GTE;

        return $this->where($field, $operator, $value)->asc($field);
    }

    /**
     * @param string ...$fields
     * @return Query|$this|self
     */
    public function asc(string ...$fields): self
    {
        foreach ($fields as $field) {
            $this->orderBy($field);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param bool $asc
     * @return Query|$this|self
     */
    public function orderBy(string $field, bool $asc = true): self
    {
        return $this->add(new OrderBy($this, $field, $asc));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param bool $including
     * @return Query|$this|self
     */
    public function before(string $field, $value, bool $including = false): self
    {
        $operator = $including ? Operator::LT : Operator::LTE;

        return $this->where($field, $operator, $value)->desc($field);
    }

    /**
     * @param string ...$fields
     * @return Query|$this|self
     */
    public function desc(string ...$fields): self
    {
        foreach ($fields as $field) {
            $this->orderBy($field, false);
        }

        return $this;
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function latest(string $field = 'createdAt'): self
    {
        return $this->desc($field);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function oldest(string $field = 'createdAt'): self
    {
        return $this->asc($field);
    }
}
