<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Common;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Tests\TestCase;

/**
 * Class FieldTestCase
 */
class FieldTestCase extends TestCase
{
    /**
     * @return void
     */
    public function testStar(): void
    {
        $field = Field::new('COUNT(*) AS example');

        $this->assertEquals('', $field->getName());
        $this->assertEquals('COUNT(*) AS example', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());
    }

    /**
     * @return void
     */
    public function testFieldExtraction(): void
    {
        $field = Field::new('example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('alias.example', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());

        // ----

        $field = Field::new('example.relation');

        $this->assertEquals('example.relation', $field->getName());
        $this->assertEquals('alias.example.relation', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());

        // ----

        $field = Field::new('example as some');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('alias.example as some', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());

        // ----

        $field = Field::new('example.relation as some');

        $this->assertEquals('example.relation', $field->getName());
        $this->assertEquals('alias.example.relation as some', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());

        // ----

        $field = Field::new('FN(example) as some');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('FN(alias.example) as some', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());

        // ----

        $field = Field::new('FN(example.relation) as some');

        $this->assertEquals('example.relation', $field->getName());
        $this->assertEquals('FN(alias.example.relation) as some', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());

        // ----

        $field = Field::new(':example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('example', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());

        // ----

        $field = Field::new(':example.relation');

        $this->assertEquals('example.relation', $field->getName());
        $this->assertEquals('example.relation', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());

        // ----

        $field = Field::new(':example as some');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('example as some', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());

        // ----

        $field = Field::new(':example.relation as some');

        $this->assertEquals('example.relation', $field->getName());
        $this->assertEquals('example.relation as some', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());

        // ----

        $field = Field::new('FN(:example) as some');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('FN(example) as some', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());

        // ----

        $field = Field::new('FN(:example.relation) as some');

        $this->assertEquals('example.relation', $field->getName());
        $this->assertEquals('FN(example.relation) as some', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSimpleField(): void
    {
        $field = new Field('example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('example', $field->toString());
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testSimpleFnField(): void
    {
        $field = new Field('FUNCTION(FN(example)) AS example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('FUNCTION(FN(example)) AS example', $field->toString());
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAdvancedField(): void
    {
        $field = new Field('example AS some');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('example AS some', $field->toString());
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAdvancedAliasedField(): void
    {
        $field = Field::new('example AS some');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('alias.example AS some', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAliasedField(): void
    {
        $field = Field::new('example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('alias.example', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testAliasedFnField(): void
    {
        $field = Field::new('FUNCTION(FN(example)) AS example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('FUNCTION(FN(alias.example)) AS example', $field->toString('alias'));
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testForceNonAliasedField(): void
    {
        $field = Field::new(':example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('example', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testForceNonAliasedFnField(): void
    {
        $field = Field::new('FUNCTION(FN(:example)) AS example');

        $this->assertEquals('example', $field->getName());
        $this->assertEquals('FUNCTION(FN(example)) AS example', $field->toString('alias'));
        $this->assertFalse($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testChunksField(): void
    {
        $field = Field::new('a.b.c');
        $chunks = \array_map('\\strval', \iterator_to_array($field->getIterator(), false));

        $this->assertArraySubset(['a', 'b', 'c'], $chunks);
        $this->assertTrue($field->isPrefixed());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testChunksFnField(): void
    {
        $field = Field::new('FUNCTION(FN(a.b.c)) AS example');

        $chunks = [];
        foreach ($field->getIterator() as $chunk) {
            $chunks[] = $chunk->getName();
        }

        $this->assertArraySubset(['a', 'b', 'c'], $chunks);
        $this->assertTrue($field->isPrefixed());
    }
}
