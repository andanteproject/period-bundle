<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

class PeriodStartDate extends AbstractPeriodFunction
{
    public const NAME = 'PERIOD_START_DATE';

    protected function getPropertyName(): string
    {
        return 'startDate';
    }
}
