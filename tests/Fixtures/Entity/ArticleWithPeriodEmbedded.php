<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;

/**
 * @ORM\Entity()
 */
class ArticleWithPeriodEmbedded
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Embedded(class="League\Period\Period", columnPrefix="period_")
     */
    private ?Period $period = null;

    public function __construct(?Period $period = null)
    {
        $this->period = $period;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): self
    {
        $this->period = $period;

        return $this;
    }
}
