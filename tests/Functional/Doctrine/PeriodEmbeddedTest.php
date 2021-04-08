<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional\Doctrine;

use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriod;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriodEmbedded;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithSequence;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Period\Period;
use League\Period\Sequence;

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
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        $article = $em->getRepository(ArticleWithPeriodEmbedded::class)->findOneBy([]);
        self::assertEquals($period, $article->getPeriod());
    }

    public function testNullEmbeddedOnDatabase(): void
    {
        $this->createSchema();
        $nullPeriod = null;
        $article = new ArticleWithPeriodEmbedded($nullPeriod);
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        $article = $em->getRepository(ArticleWithPeriodEmbedded::class)->findOneBy([]);
        self::assertEquals($nullPeriod, $article->getPeriod());
    }
}
