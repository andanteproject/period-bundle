<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ArticleWithMiddleEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id  = null;

    /**
     * @ORM\Embedded(class="Andante\PeriodBundle\Tests\Fixtures\Entity\MiddleEntity", columnPrefix="middle_entity_")
     */
    private MiddleEntity $middleEntity;

    public function __construct()
    {
        $this->middleEntity = new MiddleEntity();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMiddleEntity(): MiddleEntity
    {
        return $this->middleEntity;
    }

    public function setMiddleEntity(MiddleEntity $middleEntity): self
    {
        $this->middleEntity = $middleEntity;
        return $this;
    }
}
