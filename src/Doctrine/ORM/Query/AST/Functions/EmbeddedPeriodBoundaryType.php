<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

class EmbeddedPeriodBoundaryType extends AbstractEmbeddedPeriodFunction
{
    public const NAME = 'EMBEDDED_PERIOD_BOUNDARY_TYPE';

    protected function getPropertyName(): string
    {
        return 'boundaryType';
    }
}
