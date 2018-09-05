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
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Processor\DatabaseProcessor\Common\Expression;

/**
 * Class WhereBuilder
 */
class WhereBuilder extends Builder
{
    /**
     * TODO Add "relation.field" where clause support
     *
     * @param QueryBuilder $builder
     * @param CriterionInterface|Where $where
     * @return iterable|null
     */
    public function apply($builder, CriterionInterface $where): ?iterable
    {
        $expression = new Expression($builder, $where->getOperator(), $where->getValue());
        yield from $result = $expression->create($where->getField());

        if ($where->isAnd()) {
            $builder->andWhere($result->getReturn());
        } else {
            $builder->orWhere($result->getReturn());
        }
    }
}
