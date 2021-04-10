<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use League\Period\Period;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class MiddleEntity
{
    /**
     * @ORM\Embedded(class="League\Period\Period", columnPrefix="period_")
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
