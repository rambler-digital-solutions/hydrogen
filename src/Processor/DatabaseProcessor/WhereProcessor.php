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
use RDS\Hydrogen\Criteria\Common\Operator;
use RDS\Hydrogen\Criteria\CriterionInterface;
use RDS\Hydrogen\Criteria\Where;

/**
 * Class WhereProcessor
 */
class WhereProcessor extends CriterionProcessor
{
    /**
     * @param QueryBuilder $builder
     * @param Where|CriterionInterface $where
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $builder, CriterionInterface $where): QueryBuilder
    {
        $expr = $this->getDoctrineExpression($where, $builder->expr(), $where->getField());

        return $where->isAnd()
            ? $builder->andWhere($expr)
            : $builder->orWhere($expr);
    }

    /**
     * @param Where $where
     * @param Expr $expr
     * @param Field $field
     * @return Expr\Comparison|Expr\Func|string
     */
    protected function getDoctrineExpression(Where $where, Expr $expr, Field $field)
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
                    return $expr->isNull($this->field($field));

                case Operator::NEQ:
                    return $expr->isNull($this->field($field));
            }
        }

        switch ($operator) {
            case Operator::EQ:
                return $expr->eq(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::NEQ:
                return $expr->neq(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::GT:
                return $expr->gt(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::LT:
                return $expr->lt(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::GTE:
                return $expr->gte(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::LTE:
                return $expr->lte(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::IN:
                return $expr->in(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::NOT_IN:
                return $expr->notIn(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::LIKE:
                return $expr->like(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::NOT_LIKE:
                return $expr->notLike(
                    $this->field($field),
                    $this->param($where->getValue(), $field)
                );

            case Operator::BTW:
                return $expr->between(
                    $this->field($field),
                    $this->param($where->getValue()[0] ?? null, $field),
                    $this->param($where->getValue()[1] ?? null, $field)
                );

            case Operator::NOT_BTW:
                return \vsprintf('%s NOT BETWEEN %s AND %s', [
                    $this->field($field),
                    $this->param($where->getValue()[0] ?? null, $field),
                    $this->param($where->getValue()[1] ?? null, $field),
                ]);
        }

        $error = \sprintf('Unexpected "%s" operator type', $operator);
        throw new \InvalidArgumentException($error);
    }
}
