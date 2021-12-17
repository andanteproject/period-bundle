<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

class PeriodBoundaryType extends AbstractPeriodFunction
{
    public const NAME = 'PERIOD_BOUNDARY_TYPE';

    protected function getPropertyName(): string
    {
        return 'boundaryType';
    }
}
