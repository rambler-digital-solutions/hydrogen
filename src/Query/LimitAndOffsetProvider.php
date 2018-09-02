<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\Limit;
use RDS\Hydrogen\Criteria\Offset;
use RDS\Hydrogen\Query;

/**
 * Class ExecutionsProvider
 * @mixin Query
 */
trait LimitAndOffsetProvider
{
    /**
     * An alias of "limit(...)"
     *
     * @param int $count
     * @return Query|$this|self
     */
    public function take(int $count): self
    {
        return $this->limit($count);
    }

    /**
     * @param int $count
     * @return Query|$this|self
     */
    public function limit(int $count): self
    {
        return $this->add(new Limit($count));
    }

    /**
     * An alias of "offset(...)"
     *
     * @param int $count
     * @return Query|$this|self
     */
    public function skip(int $count): self
    {
        return $this->offset($count);
    }

    /**
     * @param int $count
     * @return Query|$this|self
     */
    public function offset(int $count): self
    {
        return $this->add(new Offset($count));
    }

    /**
     * @param int $from
     * @param int $to
     * @return Query|$this|self
     */
    public function range(int $from, int $to): self
    {
        if ($from > $to) {
            throw new \InvalidArgumentException('From value must be less than To');
        }
        return $this->limit($from)->offset($to - $from);
    }
}
