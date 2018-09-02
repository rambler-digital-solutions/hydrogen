<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use RDS\Hydrogen\Criteria\GroupBy;
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
            $this->add(new GroupBy($field));
        }

        return $this;
    }
}
