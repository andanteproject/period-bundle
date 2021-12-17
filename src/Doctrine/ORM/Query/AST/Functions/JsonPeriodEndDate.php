<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Doctrine\ORM\Query\AST\PathExpression;

class JsonPeriodEndDate extends AbstractJsonPeriodFunction
{
    public const NAME = 'JSON_PERIOD_END_DATE';

    private PathExpression $fieldPathExpression;

    protected function getPropertyName(): string
    {
        return PeriodType::END_DATE_PROPERTY;
    }
}
