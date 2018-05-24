<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria\Common;

/**
 * Class Operator
 * @internal For internal usage only
 */
class Operator
{
    public const EQ  = '=';
    public const NEQ = '<>';
    public const GT  = '>';
    public const GTE = '>=';
    public const LT  = '<';
    public const LTE = '<=';

    // X IN (...)
    public const IN     = 'IN';
    public const NOT_IN = 'NOT IN';

    // LIKE
    public const LIKE     = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';

    // BETWEEN
    public const BTW     = 'BETWEEN';
    public const NOT_BTW = 'NOT BETWEEN';

    /**
     * Mappings
     *
     * Transform the given format into normal operator format.
     */
    private const OPERATOR_MAPPINGS = [
        '=='         => self::EQ,
        '==='        => self::EQ,
        ':='         => self::EQ,
        '!='         => self::NEQ,
        '!=='        => self::NEQ,
        '!IN'        => self::NOT_IN,
        '! IN'       => self::NOT_IN,
        '<>IN'        => self::NOT_IN,
        '<> IN'       => self::NOT_IN,
        '!LIKE'      => self::NOT_LIKE,
        '! LIKE'     => self::NOT_LIKE,
        '<>LIKE'      => self::NOT_LIKE,
        '<> LIKE'     => self::NOT_LIKE,
        '..'         => self::BTW,
        '...'        => self::BTW,
        '!BETWEEN'   => self::NOT_BTW,
        '! BETWEEN'   => self::NOT_BTW,
        '<>BETWEEN'   => self::NOT_BTW,
        '<> BETWEEN'   => self::NOT_BTW,
    ];

    /**
     * @var string
     */
    private $operator;

    /**
     * Operator constructor.
     * @param string $operator
     * @param $value
     */
    public function __construct(string $operator, $value)
    {
        $this->operator = $this->normalize($operator);

        $this->detect($value);
    }

    /**
     * @param string $operator
     * @return string
     */
    private function normalize(string $operator): string
    {
        $operator = \mb_strtoupper($operator, 'UTF-8');

        return self::OPERATOR_MAPPINGS[$operator] ?? $operator;
    }

    /**
     * @param $value
     */
    private function detect($value): void
    {
        if (\is_iterable($value)) {
            switch ($this->operator) {
                case static::EQ:
                    $this->operator = static::IN;
                    break;

                case static::NEQ:
                    $this->operator = static::NOT_IN;
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @return bool
     */
    public function is(string $operator): bool
    {
        return $this->operator === \mb_strtoupper($operator, 'UTF-8');
    }
}
