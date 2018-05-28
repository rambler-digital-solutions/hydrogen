<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Repository;

use RDS\Hydrogen\Query;

/**
 * @property-read Query|$this|Repository $query
 */
interface Selectable
{
    /**
     * @param Query|null $with
     * @return Query|$this
     */
    public function query(Query $with = null): Query;

    /**
     * @param Query $query
     * @return Selectable|Query|$this
     */
    public function scope(Query $query): Selectable;
}
