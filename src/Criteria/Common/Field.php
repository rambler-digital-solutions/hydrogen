<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria\Common;

use Doctrine\ORM\Query\Lexer;

/**
 * Class Field
 */
class Field implements FieldInterface, \IteratorAggregate
{
    /**
     * Inherit value delimiter
     */
    public const DEEP_DELIMITER = '.';

    /**
     * Prefix using for disable aliasing field
     */
    public const NON_ALIASED_PREFIX = ':';

    /**
     * @var array|string[]
     */
    private $chunks = [];

    /**
     * @var bool
     */
    private $prefixed = true;

    /**
     * @var string
     */
    private $wrapper = '';

    /**
     * Field constructor.
     * @param string $query
     */
    public function __construct(string $query)
    {
        \assert(\strlen(\trim($query)) > 0);

        $this->analyseAndFill($query);

        if (\count($this->chunks) === 0) {
            $this->prefixed = false;
        }
    }

    /**
     * @param string $query
     * @return void
     */
    private function analyseAndFill(string $query): void
    {
        $analyzed = $this->analyse(new Lexer($query));
        $haystack = 0;

        foreach ($analyzed as $chunk) {
            $this->chunks[] = \ltrim($chunk, ':');
            $haystack += \strlen($chunk) + 1;
        }

        $before = \substr($query, 0, $analyzed->getReturn());
        $after  = \substr($query, $analyzed->getReturn() + \max(0, $haystack - 1));

        $this->wrapper = $before . '%s' . $after;
    }

    /**
     * @param Lexer $lexer
     * @return \Generator|string[]
     */
    private function analyse(Lexer $lexer): \Generator
    {
        [$offset, $keep] = [null, true];

        foreach ($this->lex($lexer) as $token => $lookahead) {
            switch ($token['type']) {
                case Lexer::T_OPEN_PARENTHESIS:
                    $keep = true;
                    break;

                case Lexer::T_INPUT_PARAMETER:
                    $this->prefixed = false;

                case Lexer::T_IDENTIFIER:
                    if ($lookahead['type'] === Lexer::T_OPEN_PARENTHESIS) {
                        $keep = false;
                    }

                    if ($keep) {
                        if ($offset === null) {
                            $offset = $token['position'];
                        }
                        $keep = false;
                        yield $token['value'];
                    }

                    break;

                case Lexer::T_DOT:
                    $keep = true;
                    break;

                default:
                    $keep = false;
            }
        }

        return (int)$offset;
    }

    /**
     * @param Lexer $lexer
     * @return \Generator
     */
    private function lex(Lexer $lexer): \Generator
    {
        while ($lexer->moveNext()) {
            if ($lexer->token) {
                yield $lexer->token => $lexer->lookahead;
            }
        }

        yield $lexer->token => $lexer->lookahead ?? ['type' => null, 'value' => null];
    }

    /**
     * @param string $query
     * @return Field
     */
    public static function new(string $query): self
    {
        return new static($query);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return \implode(self::DEEP_DELIMITER, $this->chunks);
    }

    /**
     * @param string|null $alias
     * @return string
     */
    public function toString(string $alias = null): string
    {
        $value = $alias && $this->prefixed
            ? \implode('.', [$alias, $this->getName()])
            : $this->getName();

        return \sprintf($this->wrapper, $value);
    }

    /**
     * @return bool
     */
    public function isPrefixed(): bool
    {
        return $this->prefixed;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return iterable|Field[]
     */
    public function getIterator(): iterable
    {
        $lastOne = \count($this->chunks) - 1;

        foreach ($this->chunks as $i => $chunk) {
            $clone = clone $this;
            $clone->chunks = [$chunk];

            yield $lastOne === $i => $clone;
        }
    }
}
