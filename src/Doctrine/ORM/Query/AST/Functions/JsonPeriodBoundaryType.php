<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;

class JsonPeriodBoundaryType extends AbstractJsonPeriodFunction
{
    public const NAME = 'JSON_PERIOD_BOUNDARY_TYPE';

    protected function getPropertyName(): string
    {
        return PeriodType::BOUNDARY_TYPE_PROPERTY;
    }
}
