<?php

namespace Andante\PeriodBundle\Tests\Doctrine\DBAL\Type;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use League\Period\Duration;
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
        self::assertSame($dbValue, $periodType->convertToDatabaseValue($period, new MySqlPlatform()));
    }

    /**
     * @dataProvider conversionTests
     */
    public function testConvertToPHPValue(?Period $period, ?string $dbValue): void
    {
        $periodType = new PeriodType();
        self::assertEquals($period, $periodType->convertToPHPValue($dbValue, new MySqlPlatform()));
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
            "Could not convert PHP value '%s' of type 'string' to type 'period'. Expected one of the following types: null, %s",
            $invalidValue,
            Period::class
        ));
        $durationType = new PeriodType();
        $durationType->convertToDatabaseValue($invalidValue, new MySqlPlatform());
    }

    public function testShouldRaiseExceptionToPHPValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' of type 'string' to type 'period'. Expected one of the following types: null, %s",
            $invalidValue,
            Period::class
        ));
        $durationType = new PeriodType();
        $durationType->convertToDatabaseValue($invalidValue, new MySqlPlatform());
    }
}
