<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use Illuminate\Contracts\Support\Arrayable;
use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\Where\Operator;
use RDS\Hydrogen\Query;

/**
 * Class Where
 */
class Where extends Criterion
{
    /**
     * @var Operator
     */
    private $operator;

    /**
     * @var Field
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $and;

    /**
     * Where constructor.
     * @param Query $query
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @param bool $and
     */
    public function __construct(Query $query, string $field, string $operator, $value, bool $and = true)
    {
        parent::__construct($query);

        $this->field = $this->field($field);
        $this->value = $this->normalizeValue($value);
        $this->operator = $this->normalizeOperator(new Operator($operator), $this->value);
        $this->and = $and;
    }

    /**
     * @param mixed $value
     * @return array|mixed
     */
    private function normalizeValue($value)
    {
        switch (true) {
            case $value instanceof Arrayable:
                return $value->toArray();

            case $value instanceof \Traversable:
                return \iterator_to_array($value);

            case \is_object($value) && \method_exists($value, '__toString'):
                return (string)$value;
        }

        return $value;
    }

    /**
     * @param Operator $operator
     * @param mixed $value
     * @return Operator
     */
    private function normalizeOperator(Operator $operator, $value): Operator
    {
        if (\is_array($value) && $operator->is(Operator::EQ)) {
            return $operator->changeTo(Operator::IN);
        }

        if (\is_array($value) && $operator->is(Operator::NEQ)) {
            return $operator->changeTo(Operator::NOT_IN);
        }

        return $operator;
    }

    /**
     * @param mixed $operator
     * @param null $value
     * @return array
     */
    public static function completeMissingParameters($operator, $value = null): array
    {
        if ($value === null) {
            [$value, $operator] = [$operator, Operator::EQ];
        }

        return [$operator, $value];
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @return Operator
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isAnd(): bool
    {
        return $this->and;
    }
}
