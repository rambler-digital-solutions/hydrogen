<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Query;

use Illuminate\Support\Str;
use RDS\Hydrogen\Query;

/**
 * Trait AliasProvider
 * @mixin Query
 */
trait AliasProvider
{
    /**
     * @var int
     */
    protected static $lastQueryId = 0;

    /**
     * @var string|null
     */
    protected $alias;

    /**
     * @return string
     */
    public function getAlias(): string
    {
        if ($this->alias === null) {
            $this->alias = $this->repository
                ? $this->createAlias($this->getRepository()->getClassName())
                : $this->createAlias();
        }

        return $this->alias;
    }

    /**
     * @param string ...$patterns
     * @return string
     */
    public function createAlias(string ...$patterns): string
    {
        if (\count($patterns)) {
            $patterns = \array_map(function(string $pattern) {
                return \preg_replace('/\W+/iu', '', \snake_case(\class_basename($pattern)));
            }, $patterns);

            $pattern = \implode('_', $patterns);

            if (\trim($pattern)) {
                return \sprintf('%s_%d', $pattern, ++static::$lastQueryId);
            }
        }

        return 'q' . Str::random(7) . '_' . ++static::$lastQueryId;
    }

    /**
     * @param string|null $pattern
     * @return string
     */
    public function createPlaceholder(string $pattern = null): string
    {
        return ':' . $this->createAlias($pattern);
    }

    /**
     * @param string $alias
     * @return Query|$this|self
     */
    public function withAlias(string $alias): Query
    {
        $this->alias = $alias;

        foreach ($this->getCriteria() as $criterion) {
            $criterion->withAlias($alias);
        }

        return $this;
    }
}
