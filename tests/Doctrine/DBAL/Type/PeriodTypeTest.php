<?php

namespace Andante\PeriodBundle\Tests\Doctrine\DBAL\Type;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use League\Period\Period;
use PHPUnit\Framework\TestCase;

class PeriodTypeTest extends TestCase
{
    /**
     * @dataProvider conversionTests
     */
    public function testConvertToDatabaseValue(?Period $period, ?string $dbValue): void
    {
        $periodType = new PeriodType();
        self::assertSame($dbValue, $periodType->convertToDatabaseValue($period, new MySQLPlatform()));
    }

    /**
     * @dataProvider conversionTests
     */
    public function testConvertToPHPValue(?Period $period, ?string $dbValue): void
    {
        $periodType = new PeriodType();
        self::assertEquals($period, $periodType->convertToPHPValue($dbValue, new MySQLPlatform()));
    }

    public function conversionTests(): array
    {
        /** @var \DateTimeImmutable $startDate */
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        /** @var \DateTimeImmutable $endDate */
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');

        return [
            [
                Period::fromDatepoint(
                    $startDate,
                    $endDate,
                    Period::INCLUDE_START_EXCLUDE_END
                ),
                '{"startDate":"2020-01-01 00:00:00","endDate":"2020-01-02 00:00:00","boundaryType":"[)"}',
            ],
            [
                null,
                null,
            ],
        ];
    }

    public function testShouldRaiseExceptionToDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' to type period. Expected one of the following types: null, %s",
            $invalidValue,
            Period::class
        ));
        $durationType = new PeriodType();
        $durationType->convertToDatabaseValue($invalidValue, new MySQLPlatform());
    }

    public function testShouldRaiseExceptionToPHPValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' to type period. Expected one of the following types: null, %s",
            $invalidValue,
            Period::class
        ));
        $durationType = new PeriodType();
        $durationType->convertToDatabaseValue($invalidValue, new MySQLPlatform());
    }
}
