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
trait WhereNullProvider
{
    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function orWhereNull(string $field): self
    {
        return $this->or()->whereNull($field);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function whereNull(string $field): self
    {
        return $this->add(new Where($this, $field, Operator::EQ, null, $this->mode()));
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function orWhereNotNull(string $field): self
    {
        return $this->or()->whereNotNull($field);
    }

    /**
     * @param string $field
     * @return Query|$this|self
     */
    public function whereNotNull(string $field): self
    {
        return $this->add(new Where($this, $field, Operator::NEQ, null, $this->mode()));
    }
}
