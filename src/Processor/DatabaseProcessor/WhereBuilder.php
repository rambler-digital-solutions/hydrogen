<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Where;
use RDS\Hydrogen\Criteria\Where\Operator;

/**
 * Class WhereBuilder
 */
class WhereBuilder extends Builder
{
    /**
     * @param QueryBuilder $builder
     * @param CriterionInterface|Where $where
     * @return \Generator
     */
    public function apply($builder, CriterionInterface $where): \Generator
    {
        $expression = $this->getDoctrineExpression($where, $builder->expr(), $where->getField());

        yield from $this->extractResult($expression, function ($expr) use ($where, $builder) {
            if ($where->isAnd()) {
                $builder->andWhere($expr);
            } else {
                $builder->orWhere($expr);
            }
        });
    }

    /**
     * @param Where $where
     * @param Expr $expr
     * @param Field $field
     * @return \Generator
     */
    protected function getDoctrineExpression(Where $where, Expr $expr, Field $field): \Generator
    {
        $operator = $where->getOperator()->toString();

        /**
         * Expr:
         * - "X IS NULL"
         * - "X IS NOT NULL"
         */
        if ($where->getValue() === null) {
            switch ($operator) {
                case Operator::EQ:
                    return $expr->isNull(yield $field);

                case Operator::NEQ:
                    return $expr->isNull(yield $field);
            }
        }

        switch ($operator) {
            case Operator::EQ:
                return $expr->eq(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::NEQ:
                return $expr->neq(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::GT:
                return $expr->gt(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::LT:
                return $expr->lt(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::GTE:
                return $expr->gte(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::LTE:
                return $expr->lte(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::IN:
                return $expr->in(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::NOT_IN:
                return $expr->notIn(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::LIKE:
                return $expr->like(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::NOT_LIKE:
                return $expr->notLike(
                    yield $field,
                    yield $field => $where->getValue()
                );

            case Operator::BTW:
                return $expr->between(
                    yield $field,
                    yield $field => $where->getValue()[0] ?? null,
                    yield $field => $where->getValue()[1] ?? null
                );

            case Operator::NOT_BTW:
                return \vsprintf('%s NOT BETWEEN %s AND %s', [
                    yield $field,
                    yield $field => $where->getValue()[0] ?? null,
                    yield $field => $where->getValue()[1] ?? null,
                ]);
        }

        $error = \sprintf('Unexpected "%s" operator type', $operator);
        throw new \InvalidArgumentException($error);
    }
}
