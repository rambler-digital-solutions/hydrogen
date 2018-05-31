<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Processor\DatabaseProcessor;

/**
 * Class CriterionProcessor
 */
abstract class CriterionProcessor implements DatabaseCriterionProcessor
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var int
     */
    private static $parameterId = 1;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var DatabaseProcessor
     */
    protected $processor;

    /**
     * CriterionProcessor constructor.
     * @param string $alias
     * @param DatabaseProcessor $processor
     */
    public function __construct(string $alias, DatabaseProcessor $processor)
    {
        $this->alias = $alias;
        $this->processor = $processor;
    }

    /**
     * Creates field name using root query.
     *
     * @param Field $field
     * @return string
     */
    protected function field(Field $field): string
    {
        return $field->withAlias($this->alias);
    }

    /**
     * Register a new native PHP value.
     *
     * @param string $value
     * @param Field|null $field
     * @return string
     */
    protected function param($value, Field $field = null): string
    {
        $alias = $this->alias($field);

        $this->parameters[$alias] = $value;

        return ':' . $alias;
    }

    /**
     * Creates an alias for field.
     *
     * @param Field|null $field
     * @return string
     */
    protected function alias(Field $field = null): string
    {
        $prefix = $field ? str_replace('.', '_', $field->getName()) : 'value';

        return $prefix . self::$parameterId++;
    }

    /**
     * @return iterable|mixed[]
     */
    public function getParameters(): iterable
    {
        yield from $this->parameters;
    }
}
