<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application;

use Doctrine\ORM\EntityRepository;
use Faker\Generator;
use RDS\Hydrogen\Hydrogen;
use RDS\Hydrogen\Query;
use RDS\Hydrogen\Tests\Application\Mock\ExampleScope;

/**
 * Class QueryTestCase
 */
abstract class QueryTestCase extends DatabaseTestCase
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var string|null
     */
    private $entity;

    /**
     * @param Generator $faker
     * @return \Generator
     */
    abstract protected function getMocks(Generator $faker): \Generator;

    /**
     * @return EntityRepository|Hydrogen
     */
    abstract protected function getRepository(): EntityRepository;

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public function setUp(): void
    {
        parent::setUp();

        foreach ($this->getMocks($this->faker()) as $entity) {
            $this->count++;
            $this->entity = \get_class($entity);
            $this->em->persist($entity);
        }

        $this->em->flush();

        \assert($this->count > 0);
        \assert($this->entity !== null);

        $this->em->clear();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testBadUsageUsingMethod(): void
    {
        $this->expectException(\LogicException::class);

        (new class { use Hydrogen; })->query();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testBadUsageUsingProperty(): void
    {
        $this->expectException(\LogicException::class);

        (new class { use Hydrogen; })->query;
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testEmptyQuery(): void
    {
        $query = Query::new();

        $this->assertCount(0, $query->getCriteria());
    }

    /**
     * @return void
     */
    public function testExternalScope(): void
    {
        $result = Query::new($this->getRepository())
            ->scope(new ExampleScope())
            ->lessThan10()
            ->get();

        $this->assertCount(\min(9, $this->count), $result);
    }

    /**
     * @return void
     */
    public function testExternalStaticScope(): void
    {
        $result = Query::new($this->getRepository())
            ->scope(new ExampleScope())
            ->lessThan5()
            ->get();

        $this->assertCount(\min(4, $this->count), $result);

        $result = Query::new($this->getRepository())
            ->scope(ExampleScope::class)
            ->lessThan5()
            ->get();

        $this->assertCount(\min(4, $this->count), $result);
    }

    /**
     * @return void
     */
    public function testGroupBy(): void
    {
        $result = Query::new($this->getRepository())
            ->groupBy('id')
            ->get();

        $this->assertCount($this->count, $result);
    }

    /**
     * @return void
     */
    public function testLimit(): void
    {
        $result = Query::new($this->getRepository())
            ->take(1)
            ->get();

        $this->assertCount(1, $result);
    }

    /**
     * @return void
     */
    public function testSkipAndLimit(): void
    {
        $result = Query::new($this->getRepository())
            ->skip(1)
            ->take(1)
            ->get();

        $this->assertEquals(2, $result[0]->id);
    }

    /**
     * @return void
     */
    public function testOrderBy(): void
    {
        $result = Query::new($this->getRepository())
            ->desc('id')
            ->get();

        $this->assertEquals($this->count, $result[0]->id);
    }

    /**
     * @return void
     */
    public function testGroups(): void
    {
        $result = Query::new($this->getRepository())
            ->where('id', 1)
            ->orWhere(function(Query $query) {
                $query->where('id', 3)
                    ->orWhere('id', 5);
            })
            ->get();

        $this->assertCount(3, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testFullSelection(): void
    {
        $this->assertCount($this->count, $this->getRepository()->query->get());
        $this->assertCount($this->count, $this->getRepository()->query->collect());

        foreach ($this->getRepository()->query->get() as $i) {
            $this->assertInstanceOf($this->entity, $i);
        }

        foreach ($this->getRepository()->query->get('id') as $i) {
            $this->assertInternalType('array', $i);

            foreach ($i as $key => $j) {
                $this->assertInternalType('int', $j);
                $this->assertEquals('id', $key);
            }
        }
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     * @throws \RuntimeException
     */
    public function testCountSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('count(id) as c')
            ->scalar('c', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->count, $result);

        // Using helper
        $result = $this->getRepository()->query->count('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->count, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testSumSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('sum(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals(($this->count * ($this->count + 1)) / 2, $result);

        // Using helper
        $result = $this->getRepository()->query->sum('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals(($this->count * ($this->count + 1)) / 2, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testAvgSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('avg(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals(ceil($this->count / 2), $result);

        // Using helper
        $result = $this->getRepository()->query->avg('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals(ceil($this->count / 2), $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testMaxSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('max(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->count, $result);

        // Using helper
        $result = $this->getRepository()->query->max('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->count, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testMinSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('min(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals(1, $result);

        // Using helper
        $result = $this->getRepository()->query->min('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals(1, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testSimpleWhereSelection(): void
    {
        $result = $this->getRepository()->query
            ->where('id', '>', 10)
            ->where('id', '<=', 20)
            ->get();

        $this->assertCount(\max(\min($this->count - 10, 10), 0), $result);
    }
}
