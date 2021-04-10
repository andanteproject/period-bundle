<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Tests\Functional;

use Andante\PeriodBundle\Tests\HttpKernel\AndantePeriodKernel;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use League\Period\Period;

class MappingTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    protected static function createKernel(array $options = []): AndantePeriodKernel
    {
        /** @var AndantePeriodKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addConfig('/config/custom_mapping.yaml');

        return $kernel;
    }

    public function testMapping(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');
        $classMetadata = $em->getClassMetadata(Period::class);
        self::assertArrayHasKey('startDate', $classMetadata->fieldMappings);
        self::assertArrayHasKey('endDate', $classMetadata->fieldMappings);
        self::assertArrayHasKey('boundaryType', $classMetadata->fieldMappings);
        self::assertSame('custom_start_date', $classMetadata->getColumnName('startDate'));
        self::assertSame('custom_end_date', $classMetadata->getColumnName('endDate'));
        self::assertSame('custom_boundary_type', $classMetadata->getColumnName('boundaryType'));
        self::assertSame(Types::DATETIME_IMMUTABLE, $classMetadata->fieldMappings['startDate']['type']);
        self::assertSame(Types::DATETIME_IMMUTABLE, $classMetadata->fieldMappings['endDate']['type']);
        self::assertSame(Types::STRING, $classMetadata->fieldMappings['boundaryType']['type']);
    }
}
