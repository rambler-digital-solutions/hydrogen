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
use RDS\Hydrogen\Query;

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
     * @var Field
     */
    private $field;

    /**
     * OrderBy constructor.
     * @param Query $query
     * @param string $field
     * @param bool $asc
     */
    public function __construct(Query $query, string $field, bool $asc = true)
    {
        parent::__construct($query);

        $this->field = $this->field($field);
        $this->asc = $asc;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
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
