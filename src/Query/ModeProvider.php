<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Having;
use RDS\Hydrogen\Criteria\HavingGroup;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Criteria\WhereGroup;
use RDS\Hydrogen\Query;

/**
 * Trait ModeProvider
 * @property-read Query|$this $or
 * @property-read Query|$this $and
 * @mixin Query
 */
trait ModeProvider
{
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
            $this->add($this->createGroup(__FUNCTION__, $group));
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
            $this->add($this->createGroup(__FUNCTION__, $group));
        }

        return $this;
    }

    /**
     * @param string $fn
     * @param \Closure $group
     * @return CriterionInterface
     */
    private function createGroup(string $fn, \Closure $group): CriterionInterface
    {
        $latest = $this->criteria->count()
            ? \get_class($this->criteria->top())
            : null;

        switch($latest) {
            case Where::class:
            case WhereGroup::class:
                return new WhereGroup($this, $group, $this->mode());

            case Having::class:
            case HavingGroup::class:
                return new HavingGroup($this, $group, $this->mode());
        }

        $error = 'Operator "%s" can be added only after Where or Having clauses, but %s given';
        $given = $latest ? \class_basename($latest) : 'none';
        throw new \LogicException(\sprintf($error, \strtoupper($fn), $given));
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
