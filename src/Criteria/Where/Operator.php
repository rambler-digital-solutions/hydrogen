<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria\Where;

use Illuminate\Support\Str;

/**
 * Class Operator
 */
final class Operator
{
    public const EQ = '=';
    public const NEQ = '<>';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';

    // X IN (...)
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';

    // LIKE
    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';

    // BETWEEN
    public const BTW = 'BETWEEN';
    public const NOT_BTW = 'NOT BETWEEN';

    /**
     * Mappings
     *
     * Transform the given format into normal operator format.
     */
    private const OPERATOR_MAPPINGS = [
        '=='       => self::EQ,
        'IS'       => self::EQ,
        '!='       => self::NEQ,
        'NOTIS'    => self::NEQ,
        '!IN'      => self::NOT_IN,
        '~'        => self::LIKE,
        '!LIKE'    => self::NOT_LIKE,
        '!~'       => self::NOT_LIKE,
        '..'       => self::BTW,
        '...'      => self::BTW,
        '!BETWEEN' => self::NOT_BTW,
        '!..'      => self::NOT_BTW,
        '!...'     => self::NOT_BTW,
    ];

    /**
     * @var string
     */
    private $operator;

    /**
     * Operator constructor.
     * @param string $operator
     */
    public function __construct(string $operator)
    {
        $this->operator = $this->normalize($operator);
    }

    /**
     * @param string $operator
     * @return string
     */
    private function normalize(string $operator): string
    {
        $upper = Str::upper($operator);

        $operator = \str_replace(' ', '', $upper);

        return self::OPERATOR_MAPPINGS[$operator] ?? $upper;
    }

    /**
     * @param string $operator
     * @return Operator
     */
    public static function new(string $operator): self
    {
        return new static($operator);
    }

    /**
     * @param string $operator
     * @return Operator
     */
    public function changeTo(string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @return bool
     */
    public function is(string $operator): bool
    {
        return $this->operator === Str::upper($operator, 'UTF-8');
    }
}
