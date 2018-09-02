<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Query;

/**
 * Interface CriterionInterface
 */
interface CriterionInterface
{
    /**
     * @return Field
     */
    public function getField(): Field;

    /**
     * @param Query $query
     * @return CriterionInterface
     */
    public function withQuery(Query $query): CriterionInterface;

    /**
     * @param string $alias
     * @return CriterionInterface
     */
    public function withAlias(string $alias): CriterionInterface;
}
