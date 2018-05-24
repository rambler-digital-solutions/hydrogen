<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Criteria\Common\Operator;

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
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $and;

    /**
     * Where constructor.
     * @param string $field
     * @param string $operator
     * @param $value
     * @param bool $and
     */
    public function __construct(string $field, string $operator, $value, bool $and = true)
    {
        parent::__construct($field);

        $this->operator = new Operator($operator, $value);
        $this->value = $value;

        $this->and = $and;
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

    /**
     * @param mixed $valueOrOperator
     * @param null $value
     * @return array
     */
    public static function completeMissingParameters($valueOrOperator, $value = null): array
    {
        if ($value === null) {
            [$value, $valueOrOperator] = [$valueOrOperator, Operator::EQ];
        }

        return [$valueOrOperator, $value];
    }
}
