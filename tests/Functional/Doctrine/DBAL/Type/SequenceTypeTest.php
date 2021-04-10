<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional\Doctrine\DBAL\Type;

use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithDuration;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithPeriod;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithSequence;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Period\Duration;
use League\Period\Period;
use League\Period\Sequence;

class SequenceTypeTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testTypeOnDatabase(): void
    {
        $this->createSchema();
        /** @var \DateTimeImmutable $startDate1 */
        $startDate1 = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        /** @var \DateTimeImmutable $endDate1 */
        $endDate1 = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');
        /** @var \DateTimeImmutable $startDate2 */
        $startDate2 = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-03 00:00:00');
        /** @var \DateTimeImmutable $endDate2 */
        $endDate2 = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-04 00:00:00');
        $sequence = new Sequence(
            Period::fromDatepoint(
                $startDate1,
                $endDate1,
                Period::INCLUDE_START_EXCLUDE_END
            ),
            Period::fromDatepoint(
                $startDate2,
                $endDate2,
                Period::EXCLUDE_ALL
            )
        );
        $article = new ArticleWithSequence($sequence);
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $em->persist($article);
        $em->flush();
        $em->clear();
        /** @var ArticleWithSequence $article */
        $article = $em->getRepository(ArticleWithSequence::class)->findOneBy([]);
        self::assertEquals($sequence, $article->getPeriod());
    }
}
