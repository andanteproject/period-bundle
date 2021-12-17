<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional\Doctrine\ORM\Query\AST\Functions;

use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriod;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriodEmbedded;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use League\Period\Period;

class PeriodFunctionsTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testWithPeriodType(): void
    {
        $this->createSchema();
        $period = new Period(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-10 00:00:00'),
            Period::INCLUDE_START_EXCLUDE_END
        );
        $article = new ArticleWithPeriod($period);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();

        /** @var EntityRepository<ArticleWithPeriod> $entityRepository */
        $entityRepository = $em->getRepository(ArticleWithPeriod::class);
        $qb = $entityRepository->createQueryBuilder('a');
        $qb->select('JSON_PERIOD_START_DATE(a.period) as startDate');
        $qb->addSelect('JSON_PERIOD_END_DATE(a.period) as endDate');
        $qb->addSelect('JSON_PERIOD_BOUNDARY_TYPE(a.period) as boundaryType');
        $results = $qb->getQuery()->getArrayResult();
        $result = \reset($results);

        self::assertEquals([
            'startDate' => '2020-01-01 00:00:00',
            'endDate' => '2020-01-10 00:00:00',
            'boundaryType' => '[)',
        ], $result);
    }

    public function testWithPeriodEmbedded(): void
    {
        $this->createSchema();
        $period = new Period(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-10 00:00:00'),
            Period::INCLUDE_START_EXCLUDE_END
        );

        $article = new ArticleWithPeriodEmbedded($period);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();

        /** @var EntityRepository<ArticleWithPeriodEmbedded> $entityRepository */
        $entityRepository = $em->getRepository(ArticleWithPeriodEmbedded::class);
        $qb = $entityRepository->createQueryBuilder('a');
        $qb->select('EMBEDDED_PERIOD_START_DATE(a.period) as startDate');
        $qb->addSelect('EMBEDDED_PERIOD_END_DATE(a.period) as endDate');
        $qb->addSelect('EMBEDDED_PERIOD_BOUNDARY_TYPE(a.period) as boundaryType');

        $results = $qb->getQuery()->getArrayResult();
        $result = \reset($results);

        self::assertEquals([
            'startDate' => '2020-01-01 00:00:00',
            'endDate' => '2020-01-10 00:00:00',
            'boundaryType' => '[)',
        ], $result);
    }
}
