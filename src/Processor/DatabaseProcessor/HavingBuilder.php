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

/**
 * Class HavingBuilder
 */
class HavingBuilder extends WhereBuilder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Having $having
     * @return \Generator
     */
    final public function apply($builder, CriterionInterface $having): \Generator
    {
        $expression = $this->getDoctrineExpression($having, $builder->expr(), $having->getField());

        yield from $this->extractResult($expression, function ($expr) use ($having, $builder) {
            if ($having->isAnd()) {
                $builder->andHaving($expr);
            } else {
                $builder->orHaving($expr);
            }
        });
    }
}
