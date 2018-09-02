<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\Group;
use RDS\Hydrogen\Criteria\Having;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Query;
use RDS\Hydrogen\Query\WhereProvider\WhereBetweenProvider;
use RDS\Hydrogen\Query\WhereProvider\WhereInProvider;
use RDS\Hydrogen\Query\WhereProvider\WhereLikeProvider;
use RDS\Hydrogen\Query\WhereProvider\WhereNullProvider;

/**
 * Trait WhereProvider
 * @property-read Query|$this $or
 * @property-read Query|$this $and
 * @mixin Query
 */
trait WhereProvider
{
    use WhereInProvider;
    use WhereLikeProvider;
    use WhereNullProvider;
    use WhereBetweenProvider;

    /**
     * - AND if true
     * - OR if false
     *
     * @var bool
     */
    protected $conjunction = true;

    /**
     * @param \Closure|null $group
     * @return Query|$this|self
     */
    public function or(\Closure $group = null): self
    {
        $this->conjunction = false;

        if ($group !== null) {
            $this->add(new Group($this, $group, $this->mode()));
        }

        return $this;
    }

    /**
     * @param \Closure|null $group
     * @return Query|$this|self
     */
    public function and(\Closure $group = null): self
    {
        $this->conjunction = true;

        if ($group !== null) {
            $this->add(new Group($this, $group, $this->mode()));
        }

        return $this;
    }

    /**
     * @param string|\Closure $field
     * @param $valueOrOperator
     * @param null $value
     * @return Query|$this|self
     */
    public function orWhere($field, $valueOrOperator = null, $value = null): self
    {
        return $this->or->where($field, $valueOrOperator, $value);
    }

    /**
     * @param string|\Closure $field
     * @param $valueOrOperator
     * @param null $value
     * @return Query|$this|self
     */
    public function where($field, $valueOrOperator = null, $value = null): self
    {
        if (\is_string($field)) {
            [$operator, $value] = Where::completeMissingParameters($valueOrOperator, $value);

            return $this->add(new Where($field, $operator, $value, $this->mode()));
        }

        if ($field instanceof \Closure) {
            return $this->add(new Group($this, $field, $this->mode()));
        }

        $error = \vsprintf('Selection set should be a type of string or Closure, but %s given', [
            \studly_case(\gettype($field)),
        ]);

        throw new \InvalidArgumentException($error);
    }

    /**
     * @return bool
     */
    protected function mode(): bool
    {
        return \tap($this->conjunction, function () {
            $this->conjunction = true;
        });
    }
}
