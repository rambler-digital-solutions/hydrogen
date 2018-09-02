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
 * Class GroupBuilder
 */
class GroupBuilder extends WhereBuilder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Group $group
     * @return \Generator
     */
    public function apply($builder, CriterionInterface $group): \Generator
    {
        $inner = $group->getQuery();
        $expr  = $builder->expr()->andX();

        foreach ($inner->getCriteria() as $where) {
            $expression = parent::getDoctrineExpression($where, $builder->expr(), $where->getField());

            yield from $this->extractResult($expression, function ($current) use ($expr, $where, $builder) {
                if ($where->isAnd()) {
                    $expr->add($current);
                } else {
                    $builder->orWhere($current);
                }
            });
        }

        $group->isAnd()
            ? $builder->andWhere($expr)
            : $builder->orWhere($expr);
    }
}
