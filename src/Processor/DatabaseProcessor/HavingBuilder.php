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
use RDS\Hydrogen\Criteria\Having;
use RDS\Hydrogen\Processor\DatabaseProcessor\Common\Expression;

/**
 * Class HavingBuilder
 */
class HavingBuilder extends WhereBuilder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Having $having
     * @return iterable|null
     */
    public function apply($builder, CriterionInterface $having): ?iterable
    {
        $expression = new Expression($builder, $having->getOperator(), $having->getValue());
        yield from $result = $expression->create($having->getField());

        if ($having->isAnd()) {
            $builder->andHaving($result->getReturn());
        } else {
            $builder->orHaving($result->getReturn());
        }
    }
}
