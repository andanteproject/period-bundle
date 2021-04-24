<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class PeriodBoundaryType extends AbstractPeriodFunction
{
    public const NAME = 'PERIOD_BOUNDARY_TYPE';

    protected function getPropertyName(): string
    {
        return 'boundaryType';
    }
}
