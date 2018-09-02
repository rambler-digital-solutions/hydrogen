<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Query;

use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Query;
use RDS\Hydrogen\Tests\TestCase;

/**
 * Class QueryTestCase
 */
abstract class QueryTestCase extends TestCase
{
    /**
     * @param \Closure $expr
     * @return mixed|CriterionInterface
     */
    protected function query(\Closure $expr): CriterionInterface
    {
        $expr($query = Query::new());

        return $query->getCriteria()->current();
    }
}
