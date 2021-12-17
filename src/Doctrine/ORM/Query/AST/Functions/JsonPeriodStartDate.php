<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;

class JsonPeriodStartDate extends AbstractJsonPeriodFunction
{
    public const NAME = 'JSON_PERIOD_START_DATE';

    protected function getPropertyName(): string
    {
        return PeriodType::START_DATE_PROPERTY;
    }
}
