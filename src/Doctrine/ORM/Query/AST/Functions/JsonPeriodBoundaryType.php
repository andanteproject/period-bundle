<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use League\Period\Period;

class JsonPeriodBoundaryType extends AbstractJsonPeriodFunction
{
    public const NAME = 'JSON_PERIOD_BOUNDARY_TYPE';

    protected function getPropertyName(): string
    {
        return PeriodType::BOUNDARY_TYPE_PROPERTY;
    }
}
