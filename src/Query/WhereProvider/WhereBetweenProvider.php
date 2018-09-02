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
trait WhereBetweenProvider
{
    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function orWhereBetween(string $field, $from, $to): self
    {
        return $this->or()->whereBetween($field, $from, $to);
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function whereBetween(string $field, $from, $to): self
    {
        return $this->add(new Where($field, Operator::BTW, [$from, $to], $this->mode()));
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function orWhereNotBetween(string $field, $from, $to): self
    {
        return $this->or()->whereNotBetween($field, $from, $to);
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Query|$this|self
     */
    public function whereNotBetween(string $field, $from, $to): self
    {
        return $this->add(new Where($field, Operator::NOT_BTW, [$from, $to], $this->mode()));
    }
}
