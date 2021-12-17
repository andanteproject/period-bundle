<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Period\Sequence;

/**
 * @ORM\Entity()
 */
class ArticleWithSequence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="sequence", nullable=true)
     */
    private ?Sequence $period = null;

    public function __construct(?Sequence $period = null)
    {
        $this->period = $period;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): ?Sequence
    {
        return $this->period;
    }

    public function setPeriod(?Sequence $period): self
    {
        $this->period = $period;

        return $this;
    }
}
