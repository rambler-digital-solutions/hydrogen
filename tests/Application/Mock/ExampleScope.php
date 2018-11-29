<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application\Mock;

use RDS\Hydrogen\Query;

/**
 * Class ExampleScope
 */
class ExampleScope
{
    /**
     * @return Query
     */
    public function lessThan10(): Query
    {
        return Query::new()->where('id', '<', 10);
    }

    /**
     * @return Query
     */
    public static function lessThan5(): Query
    {
        return Query::new()->where('id', '<', 5);
    }

    /**
     * @return int
     */
    public function scalarScope(): int
    {
        return 42;
    }

    /**
     * @return int
     */
    public static function scalarStaticScope(): int
    {
        return 42;
    }
}
