<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional\Doctrine\DBAL\Type;

use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithDuration;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Period\Duration;

class DurationTypeTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testTypeOnDatabase(): void
    {
        $this->createSchema();
        $duration = Duration::createFromDateString('1 hour');
        $article = new ArticleWithDuration($duration);
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        $article = $em->getRepository(ArticleWithDuration::class)->findOneBy([]);
        self::assertEquals($duration, $article->getDuration());
    }
}
