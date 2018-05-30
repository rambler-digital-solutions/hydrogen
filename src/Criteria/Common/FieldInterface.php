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
 * Interface FieldInterface
 */
interface FieldInterface
{
    /**
     * @return bool
     */
    public function isComposite(): bool;

    /**
     * @return bool
     */
    public function isPrefixed(): bool;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $alias
     * @return string
     */
    public function withAlias(string $alias): string;

    /**
     * @return bool
     */
    public function isFunction(): bool;

    /**
     * @return iterable|string[]
     */
    public function split(): iterable;
}
