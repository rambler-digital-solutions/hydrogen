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
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return null|string
     */
    public function getAlias(): ?string;

    /**
     * @return bool
     */
    public function isPrefixed(): bool;

    /**
     * @return iterable|string[]
     */
    public function getChunks(): iterable;
}
