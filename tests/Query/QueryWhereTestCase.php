<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Query;

use RDS\Hydrogen\Criteria\Group;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Criteria\Where\Operator;
use RDS\Hydrogen\Query;

/**
 * Class QueryWhereTestCase
 */
class QueryWhereTestCase extends QueryTestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWhereEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->where(':some', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOrWhereEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->or->where(':some', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->orWhere(':some', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWhereNotEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->where(':some',  '!=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::NEQ, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOrWhereNotEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->or->where(':some', '!=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::NEQ, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->orWhere(':some',  '!=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::NEQ, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWhereGreaterThan(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->where(':some',  '>', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::GT, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOrWhereGreaterThan(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->or->where(':some', '>', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::GT, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->orWhere(':some',  '>', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::GT, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWhereGreaterThanOrEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->where(':some',  '>=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::GTE, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOrWhereGreaterThanOrEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->or->where(':some', '>=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::GTE, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->orWhere(':some',  '>=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::GTE, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWhereLessThan(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->where(':some',  '<', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::LT, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOrWhereLessThan(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->or->where(':some', '<', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::LT, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->orWhere(':some',  '<', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::LT, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testWhereLessThanOrEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->where(':some',  '<=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::LTE, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testOrWhereLessThanOrEqual(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->or->where(':some', '<=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::LTE, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            return $query->orWhere(':some',  '<=', 23);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('some', $where->getField()->toString());
        $this->assertEquals(Operator::LTE, $where->getOperator()->toString());
        $this->assertEquals(23, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereGroup(): void
    {
        $alias = null;

        /** @var Group $group */
        $group = $this->query(function(Query $query) use (&$alias) {
            $alias = $query->getAlias();

            return $query->where(function(Query $query) {
                $query->where('some', 42);
            });
        });

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($alias, $group->getQuery()->getAlias());
        $this->assertTrue($group->isAnd());

        /** @var Where $where */
        $where = $group->getQuery()->getCriteria()->current();

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals($alias . '.some', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(42, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testGroup(): void
    {
        $alias = null;

        /** @var Group $group */
        $group = $this->query(function(Query $query) use (&$alias) {
            $alias = $query->getAlias();

            return $query->and(function(Query $query) {
                $query->where('some', 42);
            });
        });

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($alias, $group->getQuery()->getAlias());
        $this->assertTrue($group->isAnd());

        /** @var Where $where */
        $where = $group->getQuery()->getCriteria()->current();

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals($alias . '.some', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(42, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrGroup(): void
    {
        $alias = null;

        /** @var Group $group */
        $group = $this->query(function(Query $query) use (&$alias) {
            $alias = $query->getAlias();

            return $query->or(function(Query $query) {
                $query->where('some', 42);
            });
        });

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($alias, $group->getQuery()->getAlias());
        $this->assertFalse($group->isAnd());

        /** @var Where $where */
        $where = $group->getQuery()->getCriteria()->current();

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals($alias . '.some', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(42, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereInDynamicCast(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->where(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereIn(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->whereIn(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereNotInDynamicCast(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->where(':field', '!=', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereNotIn(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->whereNotIn(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereInDynamicCast(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->where(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhere(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereIn(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->whereIn(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhereIn(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereNotInDynamicCast(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->where(':field', '!=', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());

        // -----------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhere(':field', '!=', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereNotIn(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->whereNotIn(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhereNotIn(':field', [1, 2, 3]);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_IN, $where->getOperator()->toString());
        $this->assertEquals([1, 2, 3], $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereNull(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->where(':field', null);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertTrue($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->whereNull(':field');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereNull(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->where(':field', null);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->whereNull(':field');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertFalse($where->isAnd());

        // ----------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhere(':field', null);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhereNull(':field');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::EQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereNotNull(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->whereNotNull(':field');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NEQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereNotNull(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->whereNotNull(':field');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NEQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhereNotNull(':field');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NEQ, $where->getOperator()->toString());
        $this->assertEquals(null, $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereBetween(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->whereBetween(':field', 1, 2);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::BTW, $where->getOperator()->toString());
        $this->assertEquals([1, 2], $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereBetween(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->whereBetween(':field', 1, 2);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::BTW, $where->getOperator()->toString());
        $this->assertEquals([1, 2], $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhereBetween(':field', 1, 2);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::BTW, $where->getOperator()->toString());
        $this->assertEquals([1, 2], $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereNotBetween(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->whereNotBetween(':field', 1, 2);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_BTW, $where->getOperator()->toString());
        $this->assertEquals([1, 2], $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereNotBetween(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->whereNotBetween(':field', 1, 2);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_BTW, $where->getOperator()->toString());
        $this->assertEquals([1, 2], $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhereNotBetween(':field', 1, 2);
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_BTW, $where->getOperator()->toString());
        $this->assertEquals([1, 2], $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereLike(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->like(':field', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertTrue($where->isAnd());

        // ----------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->where(':field', '~', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereLike(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->like(':field', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orLike(':field', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());

        // ------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->where(':field',  '~', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhere(':field', '~', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());
    }

    /**
     * @return void
     */
    public function testWhereNotLike(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->notLike(':field', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertTrue($where->isAnd());

        // ---------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->where(':field', '!~', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertTrue($where->isAnd());
    }

    /**
     * @return void
     */
    public function testOrWhereNotLike(): void
    {
        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->notLike(':field', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orNotLike(':field', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());

        // -------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->or->where(':field', '!~', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());

        // ---------------------

        /** @var Where $where */
        $where = $this->query(function(Query $query) {
            $query->orWhere(':field', '!~', 'value');
        });

        $this->assertInstanceOf(Where::class, $where);
        $this->assertEquals('field', $where->getField()->toString());
        $this->assertEquals(Operator::NOT_LIKE, $where->getOperator()->toString());
        $this->assertEquals('value', $where->getValue());
        $this->assertFalse($where->isAnd());
    }
}
