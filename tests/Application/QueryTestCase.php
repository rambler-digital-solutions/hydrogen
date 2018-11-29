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
use RDS\Hydrogen\Tests\Application\Mock\Entity\Message;
use RDS\Hydrogen\Tests\Application\Mock\Entity\User;
use RDS\Hydrogen\Tests\Application\Mock\ExampleScope;

/**
 * Class QueryTestCase
 */
abstract class QueryTestCase extends DatabaseTestCase
{
    /**
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getEntitiesCount(): int
    {
        $builder = $this->getRepository()->createQueryBuilder('e');
        $builder->select('COUNT(e.id)');
        $builder->setMaxResults(1);

        return (int) $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * @return EntityRepository|Hydrogen
     */
    abstract protected function getRepository(): EntityRepository;

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->log(function () {
            foreach ($this->getMocks($this->faker()) as $entity) {
                $this->em->persist($entity);
            }

            $this->em->flush();
            $this->em->clear();
        });
    }

    /**
     * @param Generator $faker
     * @return \Generator
     * @throws \Exception
     */
    protected function getMocks(Generator $faker): \Generator
    {
        for ($i = \random_int(6, 20); $i--;) {
            $user = new User();
            $user->name = $faker->name;

            for ($j = \random_int(0, 15); $j--;) {
                $message = new Message();
                $message->content = $faker->text(200);
                $message->author = $user;

                if (\random_int(0, 1)) {
                    $user->likedMessages->add($message);
                }
            }

            yield $user;
        }
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testBadUsageUsingMethod(): void
    {
        $this->expectException(\LogicException::class);

        (new class
        {
            use Hydrogen;
        })->query();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\Exception
     */
    public function testBadUsageUsingProperty(): void
    {
        $this->expectException(\LogicException::class);

        (new class
        {
            use Hydrogen;
        })->query;
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testExternalScope(): void
    {
        $result = Query::new($this->getRepository())
            ->scope(new ExampleScope())
            ->lessThan10()
            ->get();

        $this->assertCount(\min(9, $this->getEntitiesCount()), $result);
    }

    /**
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testExternalStaticScope(): void
    {
        $result = Query::new($this->getRepository())
            ->scope(new ExampleScope())
            ->lessThan5()
            ->get();

        $this->assertCount(\min(4, $this->getEntitiesCount()), $result);

        $result = Query::new($this->getRepository())
            ->scope(ExampleScope::class)
            ->lessThan5()
            ->get();

        $this->assertCount(\min(4, $this->getEntitiesCount()), $result);
    }

    /**
     * @return void
     */
    public function testQueryProvidesNonQueryScopeMethod(): void
    {
        $result = Query::new()
            ->scope(new ExampleScope())
            ->scalarScope();

        $this->assertEquals(42, $result);

        $result = Query::new()
            ->scope(ExampleScope::class)
            ->scalarStaticScope();

        $this->assertEquals(42, $result);
    }

    /**
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testGroupBy(): void
    {
        $result = Query::new($this->getRepository())
            ->groupBy('id')
            ->get();

        $this->assertCount($this->getEntitiesCount(), $result);
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testOrderBy(): void
    {
        $result = Query::new($this->getRepository())
            ->desc('id')
            ->get();

        $this->assertEquals($this->getEntitiesCount(), $result[0]->id);
    }

    /**
     * @return void
     */
    public function testGroups(): void
    {
        $result = Query::new($this->getRepository())
            ->where('id', 1)
            ->orWhere(function (Query $query) {
                $query->where('id', 3)
                    ->orWhere('id', 5);
            })
            ->get();

        $this->assertCount(3, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testFullSelection(): void
    {
        $this->assertCount($this->getEntitiesCount(), $this->getRepository()->query->get());
        $this->assertCount($this->getEntitiesCount(), $this->getRepository()->query->collect());

        foreach ($this->getRepository()->query->get() as $i) {
            $this->assertInstanceOf($this->getEntityClass(), $i);
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testCountSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('count(id) as c')
            ->scalar('c', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->getEntitiesCount(), $result);

        // Using helper
        $result = $this->getRepository()->query->count('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->getEntitiesCount(), $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testSumSelection(): void
    {
        $count = $this->getEntitiesCount();

        // Native
        $result = $this->getRepository()->query
            ->select('sum(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals(($count * ($count + 1)) / 2, $result);

        // Using helper
        $result = $this->getRepository()->query->sum('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals(($count * ($count + 1)) / 2, $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAvgSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('avg(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals(ceil($this->getEntitiesCount() / 2), $result);

        // Using helper
        $result = $this->getRepository()->query->avg('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals(ceil($this->getEntitiesCount() / 2), $result);
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit\Framework\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testMaxSelection(): void
    {
        // Native
        $result = $this->getRepository()->query
            ->select('max(id) as s')
            ->scalar('s', 'int');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->getEntitiesCount(), $result);

        // Using helper
        $result = $this->getRepository()->query->max('id');

        $this->assertInternalType('int', $result);
        $this->assertEquals($this->getEntitiesCount(), $result);
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testSimpleWhereSelection(): void
    {
        $result = $this->getRepository()->query
            ->where('id', '>', 10)
            ->where('id', '<=', 20)
            ->get();

        $this->assertCount(\max(\min($this->getEntitiesCount() - 10, 10), 0), $result);
    }
}
