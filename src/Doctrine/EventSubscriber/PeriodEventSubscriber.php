<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\EventSubscriber;

use Andante\PeriodBundle\Config\Doctrine\EmbeddedPeriod\Configuration as EmbeddedPeriodConfiguration;
use Andante\PeriodBundle\PropertyAccess\PropertyAccessor;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use League\Period\Period;

class PeriodEventSubscriber implements EventSubscriber, EventSubscriberInterface
{
    private PropertyAccessor $propertyAccessor;
    private EmbeddedPeriodConfiguration $embeddedPeriodConfiguration;

    public function __construct(EmbeddedPeriodConfiguration $embeddedPeriodConfiguration)
    {
        $this->propertyAccessor = PropertyAccessor::create();
        $this->embeddedPeriodConfiguration = $embeddedPeriodConfiguration;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        $classMetadata = $eventArgs->getEntityManager()->getClassMetadata(\get_class($entity));
        // Let's search for embedded Period entities
        foreach ($classMetadata->embeddedClasses as $propertyName => $config) {
            if (Period::class === $config['class']) {
                // Yes! Let's analyze each embeddable Period property
                $embeddablePeriod = $this->propertyAccessor->getValue($entity, $propertyName);
                // Let's check if this embeddablePeriod should be null
                if ($this->propertyAccessor->isUninitialized($embeddablePeriod, 'boundaryType')) {
                    // "boundaryType" property is not initialized.
                    // This means this embeddable Period should be NULL
                    $this->propertyAccessor->setValue($entity, $propertyName, null);
                }
            }
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass) {
            return;
        }

        $entityName = $classMetadata->reflClass->getName();
        if (Period::class === $entityName) {
            $classMetadata->isEmbeddedClass = true;
            $classMetadata->mapField([
                'fieldName' => 'startDate',
                'type' => Types::DATETIME_IMMUTABLE,
                'nullable' => true,
                'columnName' => $this->embeddedPeriodConfiguration->getStartDateColumnNameForClass($entityName),
            ]);

            $classMetadata->mapField([
                'fieldName' => 'endDate',
                'type' => Types::DATETIME_IMMUTABLE,
                'nullable' => true,
                'columnName' => $this->embeddedPeriodConfiguration->getEndDateColumnNameForClass($entityName),
            ]);

            $classMetadata->mapField([
                'fieldName' => 'boundaryType',
                'type' => Types::STRING,
                'length' => 2,
                'nullable' => true,
                'columnName' => $this->embeddedPeriodConfiguration->getBoundaryTypeColumnNameForClass($entityName),
            ]);
        }
    }
}
