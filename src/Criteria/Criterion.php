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
 * Class Criterion
 */
abstract class Criterion implements CriterionInterface
{
    /**
     * @var Field
     */
    private $field;

    /**
     * Criterion constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = new Field($field);
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @param Query $query
     * @return CriterionInterface
     */
    public function withQuery(Query $query): CriterionInterface
    {
        if ($this->field) {
            $this->field->withQuery($query);
        }

        return $this;
    }

    /**
     * @param string $alias
     * @return CriterionInterface
     */
    public function withAlias(string $alias): CriterionInterface
    {
        if ($this->field) {
            $this->field->withAlias($alias);
        }

        return $this;
    }
}
