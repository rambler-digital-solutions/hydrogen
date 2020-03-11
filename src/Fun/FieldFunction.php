<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Fun;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class FieldFunction
 */
class FieldFunction extends FunctionNode
{
    /**
     * @var Literal
     */
    private $field;

    /**
     * @var Literal
     */
    private $table;

    /**
     * @var Literal
     */
    private $alias;

    /**
     * @param Parser $parser
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->table = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->alias = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $alias = $sqlWalker->getSQLTableAlias($this->table->value, $this->alias->value);

        return $alias . '.' . $this->field->value;
    }
}
