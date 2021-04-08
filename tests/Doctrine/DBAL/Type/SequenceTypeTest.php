<?php

namespace Andante\PeriodBundle\Tests\Doctrine\DBAL\Type;

use Andante\PeriodBundle\Doctrine\DBAL\Type\SequenceType;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use League\Period\Period;
use League\Period\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTypeTest extends TestCase
{
    /**
     * @dataProvider conversionTests
     */
    public function testConvertToDatabaseValue(?Sequence $period, ?string $dbValue): void
    {
        $sequenceType = new SequenceType();
        self::assertSame($dbValue, $sequenceType->convertToDatabaseValue($period, new MySqlPlatform()));
    }

    /**
     * @dataProvider conversionTests
     */
    public function testConvertToPHPValue(?Sequence $period, ?string $dbValue): void
    {
        $sequenceType = new SequenceType();
        self::assertEquals($period, $sequenceType->convertToPHPValue($dbValue, new MySqlPlatform()));
    }

    public function conversionTests(): array
    {
        return [
            [
                new Sequence(
                    new Period(
                        \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
                        \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00'),
                        Period::INCLUDE_START_EXCLUDE_END
                    ),
                    new Period(
                        \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-03 00:00:00'),
                        \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-04 00:00:00'),
                        Period::EXCLUDE_ALL
                    )
                ),
                '[{"startDate":"2020-01-01 00:00:00","endDate":"2020-01-02 00:00:00","boundaryType":"[)"},{"startDate":"2020-01-03 00:00:00","endDate":"2020-01-04 00:00:00","boundaryType":"()"}]',
            ],
            [
                null,
                null
            ]
        ];
    }

    public function testShouldRaiseExceptionToDatabaseValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' of type 'string' to type 'sequence'. Expected one of the following types: null, %s",
            $invalidValue,
            Sequence::class
        ));
        $durationType = new SequenceType();
        $durationType->convertToDatabaseValue($invalidValue, new MySqlPlatform());
    }

    public function testShouldRaiseExceptionToPHPValue(): void
    {
        $this->expectException(ConversionException::class);
        $invalidValue = 'foo';
        $this->expectDeprecationMessage(\sprintf(
            "Could not convert PHP value '%s' of type 'string' to type 'sequence'. Expected one of the following types: null, %s",
            $invalidValue,
            Sequence::class
        ));
        $durationType = new SequenceType();
        $durationType->convertToDatabaseValue($invalidValue, new MySqlPlatform());
    }
}
