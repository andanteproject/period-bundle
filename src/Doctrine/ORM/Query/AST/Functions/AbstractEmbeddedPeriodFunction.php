<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

abstract class AbstractEmbeddedPeriodFunction extends FunctionNode
{
    private PathExpression $fieldPathExpression;

    abstract protected function getPropertyName(): string;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return $this->fieldPathExpression->dispatch($sqlWalker);
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldPathExpression = $parser->StateFieldPathExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

        $this->fieldPathExpression->field .= \sprintf('.%s', $this->getPropertyName());
    }
}
