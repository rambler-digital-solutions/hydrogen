<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @return Generator
     * @throws \InvalidArgumentException
     */
    protected function faker(): Generator
    {
        static $faker;
        return $faker ?? ($faker = Factory::create());
    }
}
