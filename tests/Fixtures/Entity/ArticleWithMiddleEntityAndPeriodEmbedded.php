<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ArticleWithMiddleEntityAndPeriodEmbedded
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id  = null;

    /**
     * @ORM\Embedded(class="MiddleEntity1", columnPrefix="middle_entity_")
     */
    private MiddleEntity1 $middleEntity;

    public function __construct()
    {
        $this->middleEntity = new MiddleEntity1();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMiddleEntity(): MiddleEntity1
    {
        return $this->middleEntity;
    }

    public function setMiddleEntity(MiddleEntity1 $middleEntity): self
    {
        $this->middleEntity = $middleEntity;
        return $this;
    }
}
