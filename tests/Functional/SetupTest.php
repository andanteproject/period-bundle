<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional;

use Andante\PeriodBundle\Doctrine\DBAL\Type\DurationType;
use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Andante\PeriodBundle\Doctrine\DBAL\Type\SequenceType;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SetupTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testDoctrineTypeSetup(): void
    {
        $types = self::$container->getParameter('doctrine.dbal.connection_factory.types');

        self::assertArrayHasKey(DurationType::NAME, $types);
        self::assertSame(DurationType::class, $types[DurationType::NAME]['class']);

        self::assertArrayHasKey(PeriodType::NAME, $types);
        self::assertSame(PeriodType::class, $types[PeriodType::NAME]['class']);

        self::assertArrayHasKey(SequenceType::NAME, $types);
        self::assertSame(SequenceType::class, $types[SequenceType::NAME]['class']);
    }
}
