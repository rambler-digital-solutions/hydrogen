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
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Selection;

/**
 * Class SelectProcessor
 */
class SelectProcessor extends CriterionProcessor
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Selection $select
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $select): QueryBuilder
    {
        $field = $this->field($select->getField());

        if ($select->hasAlias()) {
            return $builder->addSelect(\sprintf('%s AS %s', $field, $select->getAlias()));
        }

        return $builder->addSelect($field);
    }
}
