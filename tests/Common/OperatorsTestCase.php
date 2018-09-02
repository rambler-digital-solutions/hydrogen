<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Common;

use RDS\Hydrogen\Criteria\Where\Operator;
use RDS\Hydrogen\Tests\TestCase;

/**
 * Class OperatorsTestCase
 */
class OperatorsTestCase extends TestCase
{
    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testEqualNormalization(): void
    {
        $this->assertTrue((new Operator('=='))->is(Operator::EQ));
        $this->assertTrue((new Operator('IS'))->is(Operator::EQ));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testNotEqualNormalization(): void
    {
        $this->assertTrue((new Operator('!='))->is(Operator::NEQ));
        $this->assertTrue((new Operator('! ='))->is(Operator::NEQ));
        $this->assertTrue((new Operator('NOTIS'))->is(Operator::NEQ));
        $this->assertTrue((new Operator('NOT IS'))->is(Operator::NEQ));
        $this->assertTrue((new Operator('<>'))->is(Operator::NEQ));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testWhereIn(): void
    {
        $this->assertTrue((new Operator('IN'))->is(Operator::IN));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testWhereNotIn(): void
    {
        $this->assertTrue((new Operator('!IN'))->is(Operator::NOT_IN));
        $this->assertTrue((new Operator('! IN'))->is(Operator::NOT_IN));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testLikeNormalization(): void
    {
        $this->assertTrue((new Operator('~'))->is(Operator::LIKE));
        $this->assertTrue((new Operator('LIKE'))->is(Operator::LIKE));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testNotLikeNormalization(): void
    {
        $this->assertTrue((new Operator('!~'))->is(Operator::NOT_LIKE));
        $this->assertTrue((new Operator('!LIKE'))->is(Operator::NOT_LIKE));

        $this->assertTrue((new Operator('! ~'))->is(Operator::NOT_LIKE));
        $this->assertTrue((new Operator('! LIKE'))->is(Operator::NOT_LIKE));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testBetweenNormalization(): void
    {
        $this->assertTrue((new Operator('..'))->is(Operator::BTW));
        $this->assertTrue((new Operator('...'))->is(Operator::BTW));
        $this->assertTrue((new Operator('BETWEEN'))->is(Operator::BTW));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testNotBetweenNormalization(): void
    {
        $this->assertTrue((new Operator('!BETWEEN'))->is(Operator::NOT_BTW));
        $this->assertTrue((new Operator('! BETWEEN'))->is(Operator::NOT_BTW));
        $this->assertTrue((new Operator('!..'))->is(Operator::NOT_BTW));
        $this->assertTrue((new Operator('! ..'))->is(Operator::NOT_BTW));
        $this->assertTrue((new Operator('!...'))->is(Operator::NOT_BTW));
        $this->assertTrue((new Operator('! ...'))->is(Operator::NOT_BTW));
    }
}
