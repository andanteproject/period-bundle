<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional\Doctrine;

use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithMiddleEntityAndPeriodEmbedded;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriodEmbedded;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Period\Period;

class PeriodEmbeddedTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testEmbeddedOnDatabase(): void
    {
        $this->createSchema();
        $period = new Period(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00'),
            Period::INCLUDE_START_EXCLUDE_END
        );
        $article = new ArticleWithPeriodEmbedded($period);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        /** @var ArticleWithPeriodEmbedded $article */
        $article = $em->getRepository(ArticleWithPeriodEmbedded::class)->findOneBy([]);
        self::assertEquals($period, $article->getPeriod());
    }

    public function testNullEmbeddedOnDatabase(): void
    {
        $this->createSchema();
        $nullPeriod = null;
        $article = new ArticleWithPeriodEmbedded($nullPeriod);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        /** @var ArticleWithPeriodEmbedded $article */
        $article = $em->getRepository(ArticleWithPeriodEmbedded::class)->findOneBy([]);
        self::assertEquals($nullPeriod, $article->getPeriod());
    }

    public function testNullEmbeddedRecursiveOnDatabase(): void
    {
        $this->createSchema();
        $nullPeriod = null;
        $article = new ArticleWithMiddleEntityAndPeriodEmbedded();
        $article->getMiddleEntity()->setPeriod($nullPeriod);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        /** @var ArticleWithMiddleEntityAndPeriodEmbedded $article */
        $article = $em->getRepository(ArticleWithMiddleEntityAndPeriodEmbedded::class)->findOneBy([]);
        self::assertEquals($nullPeriod, $article->getMiddleEntity()->getPeriod());
    }
}
