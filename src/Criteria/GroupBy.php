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
 * Class GroupBy
 */
class GroupBy extends Criterion
{
    /**
     * @var Field
     */
    private $field;

    /**
     * GroupBy constructor.
     * @param Query $query
     * @param string $field
     */
    public function __construct(Query $query, string $field)
    {
        parent::__construct($query);

        $this->field = new Field($field);
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }
}
