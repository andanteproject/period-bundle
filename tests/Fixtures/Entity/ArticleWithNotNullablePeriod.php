<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;

/**
 * @ORM\Entity()
 */
class ArticleWithNotNullablePeriod
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    private string $title = '';

    /**
     * @ORM\Column(type="period")
     */
    private Period $period;

    public function __construct(Period $period)
    {
        $this->period = $period;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): Period
    {
        return $this->period;
    }

    public function setPeriod(Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
