<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query\WhereProvider;

use RDS\Hydrogen\Criteria\Where\Operator;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Query;

/**
 * Trait WhereBetweenProvider
 * @mixin Query\WhereProvider
 */
trait WhereInProvider
{
    /**
     * @param string $field
     * @param iterable $value
     * @return Query|$this|self
     */
    public function orWhereIn(string $field, iterable $value): self
    {
        return $this->or()->whereIn($field, $value);
    }

    /**
     * @param string $field
     * @param iterable|array $value
     * @return Query|$this|self
     */
    public function whereIn(string $field, iterable $value): self
    {
        return $this->where($field, Operator::IN, $value);
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Query|$this|self
     */
    public function orWhereNotIn(string $field, iterable $value): self
    {
        return $this->or()->whereNotIn($field, $value);
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Query|$this|self
     */
    public function whereNotIn(string $field, iterable $value): self
    {
        return $this->where($field, Operator::NOT_IN, $value);
    }
}
