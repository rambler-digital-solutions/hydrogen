<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Criteria\Common\Field;

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
     * @param string|null $as
     */
    public function __construct(string $field, string $as = null)
    {
        parent::__construct($field);
        $this->as = $as;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
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
     * @return Field
     */
    public function toString(): Field
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->field->toString();
    }
}
