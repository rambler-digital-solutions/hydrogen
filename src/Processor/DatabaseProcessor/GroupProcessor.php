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
use RDS\Hydrogen\Criteria\Group;
use RDS\Hydrogen\Criteria\Where;

/**
 * Class GroupProcessor
 */
class GroupProcessor extends WhereProcessor
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Group $group
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $group): QueryBuilder
    {
        $inner = $group->getQuery();
        $expr  = $builder->expr()->andX();

        foreach ($inner->getCriteria() as $where) {
            if ($where instanceof Where) {
                $selection = parent::getDoctrineExpression($where, $builder->expr(), $where->getField());

                if ($where->isAnd()) {
                    $expr->add($selection);
                } else {
                    $builder->orWhere($selection);
                }

                continue;
            }

            throw new \InvalidArgumentException('Inner selection does not support the ' . \class_basename($where));
        }

        return $group->isAnd()
            ? $builder->andWhere($expr)
            : $builder->orWhere($expr);
    }
}
