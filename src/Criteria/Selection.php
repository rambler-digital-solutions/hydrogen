<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;
/**
 * Class Selection
 */
class Selection extends Criterion
{
    /**
     * @var string|null
     */
    private $as;

    /**
     * Select constructor.
     * @param string $field
     * @param string|null $alias
     */
    public function __construct(string $field, string $alias = null)
    {
        parent::__construct($field);

        $this->as = $alias;
    }

    /**
     * @return bool
     */
    public function hasAlias(): bool
    {
        return $this->as !== null;
    }

    /**
     * @return null|string
     */
    public function getAlias(): ?string
    {
        return $this->as;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->getField()->toString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
