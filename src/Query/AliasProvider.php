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
     * @return string|null
     */
    public function getAlias(): ?string
    {
        if ($this->alias === null) {
            $this->alias = $this->repository
                ? $this->createAlias($this->getRepository()->getClassName())
                : $this->createAlias();
        }

        return $this->alias;
    }

    /**
     * @param string|null $pattern
     * @return string
     */
    private function createAlias(string $pattern = null): string
    {
        $name = $pattern
            ? \snake_case(\class_basename($pattern))
            : 'q' . Str::random(7);

        return \sprintf('%s_%d', $name, ++static::$lastQueryId);
    }

    /**
     * @param string|null $pattern
     * @return string
     */
    public function placeholder(string $pattern = null): string
    {
        return ':' . $this->createAlias($pattern);
    }
}
