<?php

namespace Andante\PeriodBundle\Tests\Doctrine\DBAL\Type;

use Andante\PeriodBundle\Doctrine\DBAL\Type\DurationType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use League\Period\Duration;
use PHPUnit\Framework\TestCase;

class DurationTypeTest extends TestCase
{
    /**
     * @dataProvider conversionTests
     */
    public function testConvertToDatabaseValue(?Duration $duration, ?string $dbValue): void
    {
        $durationType = new DurationType();
        self::assertSame($dbValue, $durationType->convertToDatabaseValue($duration, new MySQLPlatform()));
    }

    public function conversionTests(): array
    {
        return [
            [
                Duration::createFromDateString('1 hour'),
                '+P00Y00M00DT01H00M00S',
            ],
            [
                null,
                null,
            ],
        ];
    }

    /**
     * @dataProvider conversionTests
     */
    public function testConvertToPHPValue(?Duration $duration, ?string $dbValue): void
    {
        $durationType = new DurationType();
        self::assertEquals($duration, $durationType->convertToPHPValue($dbValue, new MySQLPlatform()));
    }

    public function testShouldRaiseExceptionToDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' to type duration. Expected one of the following types: null, %s",
            $invalidValue,
            Duration::class
        ));
        $durationType = new DurationType();
        $durationType->convertToDatabaseValue($invalidValue, new MySQLPlatform());
    }

    public function testShouldRaiseExceptionToPHPValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' to type duration. Expected one of the following types: null, %s",
            $invalidValue,
            Duration::class
        ));
        $durationType = new DurationType();
        $durationType->convertToDatabaseValue($invalidValue, new MySQLPlatform());
    }
}
