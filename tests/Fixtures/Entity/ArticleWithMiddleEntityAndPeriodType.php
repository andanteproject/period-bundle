<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ArticleWithMiddleEntityAndPeriodType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Embedded(class="MiddleEntity2", columnPrefix="middle_entity_")
     */
    private MiddleEntity2 $middleEntity;

    public function __construct()
    {
        $this->middleEntity = new MiddleEntity2();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMiddleEntity(): MiddleEntity2
    {
        return $this->middleEntity;
    }

    public function setMiddleEntity(MiddleEntity2 $middleEntity): self
    {
        $this->middleEntity = $middleEntity;

        return $this;
    }
}
