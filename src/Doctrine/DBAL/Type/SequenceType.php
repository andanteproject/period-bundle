<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use League\Period\Exception;
use League\Period\Period;
use League\Period\Sequence;

class SequenceType extends JsonType
{
    public const NAME = 'sequence';

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Sequence) {
            return parent::convertToDatabaseValue(
                \array_map(
                    static fn (Period $period): array => PeriodType::normalizePeriod(
                        $period,
                        $platform->getDateTimeTzFormatString()
                    ),
                    $value->toArray()
                ),
                $platform
            );
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', Sequence::class]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Sequence
    {
        if ($value === null || $value instanceof Sequence) {
            return $value;
        }

        try {
            return new Sequence(
                ...\array_map(
                    static fn (array $data): Period => PeriodType::denormalizePeriod(
                        $data,
                        $platform->getDateTimeTzFormatString()
                    ),
                    parent::convertToPHPValue($value, $platform)
                )
            );
        } catch (Exception $e) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeTzFormatString()
            );
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
