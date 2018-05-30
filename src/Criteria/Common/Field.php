<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria\Common;

/**
 * Class Field
 */
class Field implements FieldInterface
{
    /**
     * Inherit value delimiter
     */
    public const DEEP_DELIMITER = '.';

    /**
     * Prefix using for disable aliasing field
     */
    public const NON_ALIASED_PREFIX = ':';

    /**
     * @var bool
     */
    private $hasFunction = false;

    /**
     * @var bool
     */
    private $isAliased = true;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $wrapper = '%s';

    /**
     * Field constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        \assert(\strlen($field) > 0);

        $this->field = $this->extractFieldPrefixLogic(
            $this->extractFieldFromFunction($field)
        );

    }

    /**
     * @param string $field
     * @return string
     */
    private function extractFieldPrefixLogic(string $field): string
    {
        if (\strpos($field, self::NON_ALIASED_PREFIX) === 0) {
            $this->isAliased = false;

            return \substr($field, \strlen(self::NON_ALIASED_PREFIX));
        }

        return $field;
    }

    /**
     * @param string $field
     * @return string
     */
    private function extractFieldFromFunction(string $field): string
    {
        $pattern = '/\(([\w|\.|\:]+)\)/u';
        \preg_match($pattern, $field, $chunks);

        if (\count($chunks)) {
            $this->wrapper = \str_replace($chunks[1], '%s', $chunks[0]);
            $this->hasFunction = true;

            return $chunks[1];
        }

        return $field;
    }

    /**
     * @return bool
     */
    public function isComposite(): bool
    {
        return \substr_count($this->field, self::DEEP_DELIMITER) > 0;
    }

    /**
     * @param string $alias
     * @return string
     */
    public function withAlias(string $alias): string
    {
        $field = $this->isAliased
            ? \implode(self::DEEP_DELIMITER, [$alias, $this->field])
            : $this->field;

        return \sprintf($this->wrapper, $field);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->field;
    }

    /**
     * @return bool
     */
    public function isPrefixed(): bool
    {
        return $this->isAliased;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->field;
    }

    /**
     * @return bool
     */
    public function isFunction(): bool
    {
        return $this->hasFunction;
    }

    /**
     * @return iterable|Field[]
     */
    public function split(): iterable
    {
        foreach (\explode(self::DEEP_DELIMITER, $this->field) as $chunk) {
            yield new static($chunk);
        }
    }
}
