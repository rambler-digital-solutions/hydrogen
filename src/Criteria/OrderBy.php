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
 * Class OrderBy
 */
class OrderBy extends Criterion
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * @var bool
     */
    private $asc;

    /**
     * OrderBy constructor.
     * @param string $field
     * @param bool $asc
     */
    public function __construct(string $field, bool $asc = true)
    {
        parent::__construct($field);
        $this->asc = $asc;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->asc ? static::ASC : static::DESC;
    }

    /**
     * @return bool
     */
    public function isAsc(): bool
    {
        return $this->asc;
    }

    /**
     * @return bool
     */
    public function isDesc(): bool
    {
        return ! $this->asc;
    }
}
