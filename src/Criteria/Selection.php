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
 * Class Selection
 */
class Selection extends Criterion
{
    /**
     * @var Field
     */
    private $field;

    /**
     * @var string|null
     */
    private $as;

    /**
     * Selection constructor.
     * @param Query $query
     * @param string $field
     * @param string|null $alias
     */
    public function __construct(Query $query, string $field, string $alias = null)
    {
        parent::__construct($query);

        $this->field = $this->field($field);
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
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }
}
