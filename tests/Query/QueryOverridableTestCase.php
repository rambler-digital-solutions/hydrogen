<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Query;

use RDS\Hydrogen\Query;
use RDS\Hydrogen\Tests\Application\Mock\ExampleScope;
use RDS\Hydrogen\Tests\TestCase;

/**
 * Class QueryOverridedTestCase
 */
class QueryOverridableTestCase extends TestCase
{
    public function queryProvider(): array
    {
        return [
            'Query::__call' => [
                new class extends Query
                {
                    public function __call($method, $arguments)
                    {
                        return parent::__call($method, $arguments);
                    }
                },
            ],

            'Query::__call with typehints' => [
                new class extends Query
                {
                    public function __call(string $method, array $arguments)
                    {
                        return parent::__call($method, $arguments);
                    }
                },
            ],
        ];
    }

    /**
     * @dataProvider queryProvider
     * @param Query $query
     */
    public function testCanOverride(Query $query): void
    {
        $this->assertEquals(ExampleScope::scalarStaticScope(), $query->scope(new ExampleScope())->scalarStaticScope());
    }
}
