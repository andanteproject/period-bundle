<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;

/**
 * @ORM\Embeddable()
 */
class MiddleEntity2
{
    /**
     * @ORM\Column(type="period", nullable=true)
     */
    private ?Period $period = null;

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): void
    {
        $this->period = $period;
    }
}
