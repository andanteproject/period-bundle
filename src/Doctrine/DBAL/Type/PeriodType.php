<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use League\Period\Exception;
use League\Period\Period;

class PeriodType extends JsonType
{
    public const NAME = 'period';

    public const START_DATE_PROPERTY = 'startDate';
    public const END_DATE_PROPERTY = 'endDate';
    public const BOUNDARY_TYPE_PROPERTY = 'boundaryType';

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof Period) {
            return parent::convertToDatabaseValue(
                self::normalizePeriod($value, $platform->getDateTimeTzFormatString()),
                $platform
            );
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', Period::class]);
    }

    public static function normalizePeriod(Period $period, string $datetimeFormat): array
    {
        return [
            self::START_DATE_PROPERTY => $period->getStartDate()->format($datetimeFormat),
            self::END_DATE_PROPERTY => $period->getEndDate()->format($datetimeFormat),
            self::BOUNDARY_TYPE_PROPERTY => $period->getBoundaryType(),
        ];
    }

    /**
     * @throws Exception
     */
    public static function denormalizePeriod(array $value, string $datetimeFormat): Period
    {
        $startDateStr = $value[self::START_DATE_PROPERTY] ?? null;
        $endDateStr = $value[self::END_DATE_PROPERTY] ?? null;
        $boundaryType = $value[self::BOUNDARY_TYPE_PROPERTY] ?? null;

        /** @var \DateTimeImmutable $startDate */
        $startDate = \DateTimeImmutable::createFromFormat($datetimeFormat, (string) $startDateStr);
        /** @var \DateTimeImmutable $endDate */
        $endDate = \DateTimeImmutable::createFromFormat($datetimeFormat, (string) $endDateStr);

        return Period::fromDatepoint(
            $startDate,
            $endDate,
            (string) $boundaryType
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Period
    {
        if (null === $value || $value instanceof Period) {
            return $value;
        }

        try {
            /** @var array|null $decodedValue */
            $decodedValue = parent::convertToPHPValue($value, $platform);
            if (null === $decodedValue) {
                return null;
            }

            return self::denormalizePeriod($decodedValue, $platform->getDateTimeTzFormatString());
        } catch (Exception $e) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString(), $e);
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
