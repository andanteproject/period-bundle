<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateIntervalType;
use League\Period\Duration;

class DurationType extends DateIntervalType
{
    public const NAME = 'duration';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Duration
    {
        /** @var \DateInterval|null $value */
        $value = parent::convertToPHPValue($value, $platform);
        if (null === $value) {
            return null;
        }
        return Duration::createFromDateInterval($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        try {
            return parent::convertToDatabaseValue($value, $platform);
        } catch (ConversionException $e) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', Duration::class],
                $e
            );
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
