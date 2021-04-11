<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class EmbeddedPeriodEndDate extends AbstractEmbeddedPeriodFunction
{
    public const NAME = 'EMBEDDED_PERIOD_END_DATE';

    protected function getPropertyName(): string
    {
        return 'endDate';
    }
}
