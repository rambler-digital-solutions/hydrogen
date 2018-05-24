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
class Field
{
    /**
     * Inherit value delimiter
     */
    public const DEEP_DELIMITER = '.';

    /**
     * @var string
     */
    private $field;

    /**
     * @var bool|null
     */
    private $isComposite;

    /**
     * @var string|null
     */
    private $function;

    /**
     * Field constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field       = $field;
        $this->isComposite = \substr_count($field, self::DEEP_DELIMITER) > 0;

        $this->parseFunction($field);
    }

    /**
     * @param string $field
     */
    private function parseFunction(string $field): void
    {
        $isFunction = \substr_count($field, '(') > 0;

        if ($isFunction) {
            [$this->function, $this->field] =
                \array_filter((array)\preg_split('/[\(\)]/u', $field));
        }
    }

    /**
     * @return bool
     */
    public function isComposite(): bool
    {
        return $this->isComposite;
    }

    /**
     * @param string $alias
     * @return string
     */
    public function withAlias(string $alias): string
    {
        $field = $this->fieldWithAlias($alias);

        if ($this->isFunction()) {
            return \sprintf('%s(%s)', $this->function, $field);
        }

        return $field;
    }

    /**
     * @param string $alias
     * @return string
     */
    public function fieldWithAlias(string $alias): string
    {
        $field = $this->field;

        if ($this->isComposite) {
            $parts = $this->toArray();

            \array_shift($parts);

            $field = \implode(self::DEEP_DELIMITER, $parts);
        }

        return \implode(self::DEEP_DELIMITER, [$alias, $field]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \explode(self::DEEP_DELIMITER, $this->field);
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
     * @return string
     */
    public function getName(): string
    {
        return \array_last($this->toArray());
    }

    /**
     * @return bool
     */
    public function isFunction(): bool
    {
        return $this->function !== null;
    }

    /**
     * @return null|string
     */
    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @return \Traversable|Field[]
     */
    public function split(): \Traversable
    {
        foreach ($this->toArray() as $chunk) {
            yield new static($chunk);
        }
    }
}
