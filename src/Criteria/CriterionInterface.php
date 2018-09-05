<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Query;

/**
 * Interface CriterionInterface
 */
interface CriterionInterface
{
    /**
     * @param Query $query
     * @return CriterionInterface
     */
    public function attach(Query $query): CriterionInterface;

    /**
     * @return bool
     */
    public function isAttached(): bool;

    /**
     * @return Query
     */
    public function getQuery(): Query;

    /**
     * @return string
     */
    public function getQueryAlias(): string;
}
