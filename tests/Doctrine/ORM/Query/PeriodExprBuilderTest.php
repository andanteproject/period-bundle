<?php

namespace Andante\PeriodBundle\Tests\Doctrine\ORM\Query;

use Andante\PeriodBundle\Doctrine\ORM\Query\PeriodExprBuilder;
use Andante\PeriodBundle\Exception\InvalidPeriodPathExpression;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithMiddleEntityAndPeriodEmbedded;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithMiddleEntityAndPeriodType;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriod;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriodEmbedded;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use League\Period\Period;

class PeriodExprBuilderTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @param class-string $class
     *
     * @dataProvider entitiesWithPeriodPropertyArray
     */
    public function testExtractionFunctionsOld(string $class): void
    {
        $this->createSchema();
        $period = new Period(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-10 00:00:00'),
            Period::INCLUDE_START_EXCLUDE_END
        );
        $article = new $class($period);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();

        /** @var EntityRepository<mixed> $entityRepository */
        $entityRepository = $em->getRepository($class);
        $qb = $entityRepository->createQueryBuilder('a');
        $peb = PeriodExprBuilder::create($qb);
        $qb->select($peb->getStartDate('a.period').' as startDate');
        $qb->addSelect($peb->getEndDate('a.period').' as endDate');
        $qb->addSelect($peb->getBoundaryType('a.period').' as boundaryType');
        $results = $qb->getQuery()->getArrayResult();
        $result = \reset($results);

        self::assertEquals([
            'startDate' => '2020-01-01 00:00:00',
            'endDate' => '2020-01-10 00:00:00',
            'boundaryType' => '[)',
        ], $result);
    }

    /**
     * @param class-string $class
     *
     * @dataProvider entitiesWithPeriodPropertyArray
     */
    public function testExtractionFunctions(string $class): void
    {
        $this->createSchema();
        $period = new Period(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-10 00:00:00'),
            Period::INCLUDE_START_EXCLUDE_END
        );
        $article = new $class($period);
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();

        /** @var EntityRepository<mixed> $entityRepository */
        $entityRepository = $em->getRepository($class);
        $qb = $entityRepository->createQueryBuilder('a');
        $qb->select('PERIOD_START_DATE(a.period) as startDate');
        $qb->addSelect('PERIOD_END_DATE(a.period) as endDate');
        $qb->addSelect('PERIOD_BOUNDARY_TYPE(a.period) as boundaryType');
        $results = $qb->getQuery()->getArrayResult();
        $result = \reset($results);

        self::assertEquals([
            'startDate' => '2020-01-01 00:00:00',
            'endDate' => '2020-01-10 00:00:00',
            'boundaryType' => '[)',
        ], $result);
    }

    public function entitiesWithPeriodPropertyArray(): array
    {
        return [
            [ArticleWithPeriod::class],
            [ArticleWithPeriodEmbedded::class],
        ];
    }

    /**
     * @dataProvider isEmbeddedPeriodPropertyPathTests
     */
    public function testIsEmbeddedPeriodPropertyPath(string $class, string $propertyPath, bool $expectedResult): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $qb = new QueryBuilder($em);
        $qb->from($class, 'a');
        $periodExprBuilder = PeriodExprBuilder::create($qb);
        $method = new \ReflectionMethod(PeriodExprBuilder::class, 'isEmbeddedPeriodPropertyPath');
        $method->setAccessible(true);
        self::assertSame($expectedResult, $method->invokeArgs($periodExprBuilder, ['a.'.$propertyPath]));
    }

    public function isEmbeddedPeriodPropertyPathTests(): array
    {
        return [
            [ArticleWithPeriodEmbedded::class, 'period', true],
            [ArticleWithPeriod::class, 'period', false],
            [ArticleWithMiddleEntityAndPeriodEmbedded::class, 'middleEntity.period', true],
            [ArticleWithMiddleEntityAndPeriodType::class, 'middleEntity.period', false],
        ];
    }

    /**
     * @dataProvider isEmbeddedPeriodWrongPropertyPathTests
     */
    public function testIsEmbeddedPeriodWrongPropertyPath(string $path): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $qb = new QueryBuilder($em);
        $qb->from(ArticleWithPeriod::class, 'a');
        $periodExprBuilder = PeriodExprBuilder::create($qb);
        $method = new \ReflectionMethod(PeriodExprBuilder::class, 'isEmbeddedPeriodPropertyPath');
        $method->setAccessible(true);
        $this->expectException(InvalidPeriodPathExpression::class);
        $method->invokeArgs($periodExprBuilder, [$path]);
    }

    public function isEmbeddedPeriodWrongPropertyPathTests(): array
    {
        return [
            ['a.id'],
            ['a.period.startDate'],
        ];
    }
}
