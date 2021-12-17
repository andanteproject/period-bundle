<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

class EmbeddedPeriodStartDate extends AbstractEmbeddedPeriodFunction
{
    public const NAME = 'EMBEDDED_PERIOD_START_DATE';

    protected function getPropertyName(): string
    {
        return 'startDate';
    }
}
