<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria\Criterion;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\WhereGroup;
use RDS\Hydrogen\Criteria\Having;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Processor\DatabaseProcessor\Common\Expression;

/**
 * Class HavingGroupBuilder
 */
class HavingGroupBuilder extends GroupBuilder
{
    /**
     * @var string[]|Criterion[]
     */
    protected const ALLOWED_INNER_TYPES = [
        Where::class      => 'applyWhere',
        Having::class     => 'applyWhere',
        WhereGroup::class => 'applyGroup',
    ];

    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|WhereGroup $group
     * @return iterable|null
     */
    public function apply($builder, CriterionInterface $group): ?iterable
    {
        $expression = $builder->expr()->andX();

        foreach ($this->getInnerSelections($group) as $criterion => $fn) {
            yield from $fn($builder, $expression, $criterion);
        }

        return $group->isAnd() ? $builder->andHaving($expression) : $builder->orHaving($expression);
    }

    /**
     * @param QueryBuilder $builder
     * @param Andx $context
     * @param Where $where
     * @return \Generator
     */
    final protected function applyWhere(QueryBuilder $builder, Andx $context, Where $where): \Generator
    {
        $expression = new Expression($builder, $where->getOperator(), $where->getValue());
        yield from $result = $expression->create($where->getField());

        if ($where->isAnd()) {
            $context->add($result->getReturn());
        } else {
            $builder->orHaving($result->getReturn());
        }
    }
}
