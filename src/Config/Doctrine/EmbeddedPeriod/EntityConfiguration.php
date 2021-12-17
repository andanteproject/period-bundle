<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Config\Doctrine\EmbeddedPeriod;

class EntityConfiguration
{
    private ?string $startDateColumnName = null;
    private ?string $endDateColumnName = null;
    private ?string $boundaryTypeColumnName = null;

    public function getStartDateColumnName(): ?string
    {
        return $this->startDateColumnName;
    }

    public function setStartDateColumnName(?string $startDateColumnName): self
    {
        $this->startDateColumnName = $startDateColumnName;

        return $this;
    }

    public function getEndDateColumnName(): ?string
    {
        return $this->endDateColumnName;
    }

    public function setEndDateColumnName(?string $endDateColumnName): self
    {
        $this->endDateColumnName = $endDateColumnName;

        return $this;
    }

    public function getBoundaryTypeColumnName(): ?string
    {
        return $this->boundaryTypeColumnName;
    }

    public function setBoundaryTypeColumnName(?string $boundaryTypeColumnName): self
    {
        $this->boundaryTypeColumnName = $boundaryTypeColumnName;

        return $this;
    }

    public static function createFromArray(array $config, EntityConfiguration $fallbackConfig = null): self
    {
        $entityConfiguration = new self();
        if (\array_key_exists('start_date_column_name', $config)) {
            $entityConfiguration->setStartDateColumnName($config['start_date_column_name']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setStartDateColumnName($fallbackConfig->getStartDateColumnName());
        }
        if (\array_key_exists('end_date_column_name', $config)) {
            $entityConfiguration->setEndDateColumnName($config['end_date_column_name']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setEndDateColumnName($fallbackConfig->getEndDateColumnName());
        }

        if (\array_key_exists('boundary_type_column_name', $config)) {
            $entityConfiguration->setBoundaryTypeColumnName($config['boundary_type_column_name']);
        } elseif (null !== $fallbackConfig) {
            $entityConfiguration->setBoundaryTypeColumnName($fallbackConfig->getBoundaryTypeColumnName());
        }

        return $entityConfiguration;
    }
}
