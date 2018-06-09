<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\OrderBy;

/**
 * Class OrderByProcessor
 */
class OrderByProcessor extends CriterionProcessor
{
    /**
     * @var int
     */
    private static $relationId = 0;

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|OrderBy $orderBy
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $orderBy): QueryBuilder
    {
        return $builder->orderBy($this->field($orderBy->getField()), $orderBy->getDirection());
    }

    /**
     * @param Field $field
     * @return string
     */
    protected function field(Field $field): string
    {
        $relations = explode('.', $field->getName());
        $fieldValue = array_pop($relations);

        foreach ($relations as $relation) {
            $alias = $this->createAlias($relation);
        }

        if (isset($alias)) {
            return $alias.'.'.$fieldValue;
        }

        return parent::field($field);
    }


    /**
     * @param string $class
     * @return string
     */
    private function createAlias(string $class): string
    {
        return 'ref_' . \snake_case(\class_basename($class)) . '_' . ++self::$relationId;
    }
}
