<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria\Common;

use Illuminate\Support\Str;
use RDS\Hydrogen\Query;

/**
 * Class Field
 */
class Field implements FieldInterface
{
    /**
     * @var string
     */
    private const EXTRACTION_PATTERN = '/^(?:.*?\(([\w\.\:\*]+?)\).*?|([\w\.\:\*]+)).*?$/su';

    /**
     * Inherit value delimiter
     */
    public const DEEP_DELIMITER = '.';

    /**
     * Prefix using for disable aliasing field
     */
    public const NON_ALIASED_PREFIX = ':';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $wrapper;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var bool
     */
    private $prefixed;

    /**
     * Field constructor.
     * @param string $query
     */
    public function __construct(string $query)
    {
        \assert(\strlen(\trim($query)) > 0);

        [$this->wrapper, $this->field, $this->prefixed] = $this->analyze(\trim($query));
    }

    /**
     * @param string $query
     * @return Field|static
     */
    public static function new(string $query): self
    {
        return new static($query);
    }

    /**
     * @return iterable|string[]
     */
    public function getChunks(): iterable
    {
        return \explode(self::DEEP_DELIMITER, $this->field);
    }

    /**
     * @return bool
     */
    public function isPrefixed(): bool
    {
        return $this->prefixed;
    }

    /**
     * @param string $query
     * @return array
     */
    private function analyze(string $query): array
    {
        $field = null;

        $replacement = function (array $matches) use (&$field) {
            $field = $matches[1] ?: ($matches[2] ?? null);

            return \str_replace_first($field, '%s', $matches[0]);
        };

        $wrapper = \preg_replace_callback(self::EXTRACTION_PATTERN, $replacement, $query);

        if ($field === null || $wrapper === null) {
            $error = \sprintf('Can not extract field name from %s expression', $query);
            throw new \LogicException($error);
        }

        return \array_merge([$wrapper], $this->analyzePrefix((string)$field));
    }

    /**
     * @param string $field
     * @return array
     */
    private function analyzePrefix(string $field): array
    {
        $prefixed = true;

        if ($field === '*') {
            return [$field, false];
        }

        if (Str::startsWith($field, self::NON_ALIASED_PREFIX)) {
            return [\substr($field, 1), false];
        }

        return [$field, $prefixed];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return \array_first($this->getChunks());
    }

    /**
     * @return string
     */
    private function getFieldWithPrefix(): string
    {
        if ($this->prefixed) {
            $chunks = \array_filter([$this->getAlias(), $this->field]);

            return \implode(self::DEEP_DELIMITER, $chunks);
        }

        return $this->field;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return \sprintf($this->wrapper, $this->getFieldWithPrefix());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return null|string
     */
    public function getAlias(): ?string
    {
        if ($this->alias) {
            return $this->alias;
        }

        if ($this->query) {
            return $this->query->getAlias();
        }

        return null;
    }

    /**
     * @param Query $query
     * @return Field|$this
     */
    public function withQuery(Query $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $alias
     * @return Field
     */
    public function withAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
