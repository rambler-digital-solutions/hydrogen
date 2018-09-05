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
use RDS\Hydrogen\Query;

/**
 * Trait WhereBetweenProvider
 * @mixin Query\WhereProvider
 */
trait WhereLikeProvider
{
    /**
     * @param string $field
     * @param string|mixed $value
     * @return Query|$this
     */
    public function like(string $field, $value): self
    {
        return $this->where($field, Operator::LIKE, $value);
    }

    /**
     * @param string $field
     * @param string|mixed $value
     * @return Query|$this
     */
    public function notLike(string $field, $value): self
    {
        return $this->where($field, Operator::NOT_LIKE, $value);
    }

    /**
     * @param string $field
     * @param string|mixed $value
     * @return Query|$this
     */
    public function orLike(string $field, $value): self
    {
        return $this->or()->where($field, Operator::LIKE, $value);
    }

    /**
     * @param string $field
     * @param string|mixed $value
     * @return Query|$this
     */
    public function orNotLike(string $field, $value): self
    {
        return $this->or()->where($field, Operator::NOT_LIKE, $value);
    }
}
