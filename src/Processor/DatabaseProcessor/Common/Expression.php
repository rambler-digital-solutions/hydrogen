<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Processor\DatabaseProcessor\Common;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\Where\Operator;

/**
 * Class Expression
 */
class Expression
{
    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * @var Operator
     */
    private $operator;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Expression constructor.
     * @param QueryBuilder $builder
     * @param Operator $operator
     * @param mixed $value
     */
    public function __construct(QueryBuilder $builder, Operator $operator, $value)
    {
        $this->builder = $builder;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @param Field $field
     * @return Expr\Comparison|Expr\Func|string|\Generator
     */
    public function create(Field $field): \Generator
    {
        $expr = $this->builder->expr();
        $operator = $this->operator->toString();

        /**
         * Expr:
         * - "X IS NULL"
         * - "X IS NOT NULL"
         */
        if ($this->value === null) {
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
                    yield $field => $this->value
                );

            case Operator::NEQ:
                return $expr->neq(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::GT:
                return $expr->gt(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::LT:
                return $expr->lt(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::GTE:
                return $expr->gte(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::LTE:
                return $expr->lte(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::IN:
                return $expr->in(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::NOT_IN:
                return $expr->notIn(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::LIKE:
                return $expr->like(
                    yield $field,
                    yield $field => $this->value
                );
            case Operator::NOT_LIKE:
                return $expr->notLike(
                    yield $field,
                    yield $field => $this->value
                );

            case Operator::BTW:
                return $expr->between(
                    yield $field,
                    yield $field => $this->value[0] ?? null,
                    yield $field => $this->value[1] ?? null
                );

            case Operator::NOT_BTW:
                return \vsprintf('%s NOT BETWEEN %s AND %s', [
                    yield $field,
                    yield $field => $this->value[0] ?? null,
                    yield $field => $this->value[1] ?? null,
                ]);
        }

        $error = \sprintf('Unexpected "%s" operator type', $operator);
        throw new \InvalidArgumentException($error);
    }
}
