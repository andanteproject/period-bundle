<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

class PeriodEndDate extends AbstractPeriodFunction
{
    public const NAME = 'PERIOD_END_DATE';

    protected function getPropertyName(): string
    {
        return 'endDate';
    }
}
