<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\EventSubscriber;

use Andante\PeriodBundle\PropertyAccess\PropertyAccessor;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use League\Period\Period;

class PeriodEventSubscriber implements EventSubscriber
{
    private array $entitiesWithEmbeddedPeriod = [];

    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccessor::create();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $object = $eventArgs->getObject();
        foreach (\array_keys($this->entitiesWithEmbeddedPeriod) as $entityWithEmbeddedPeriod) {
            // Has loaded entity an embeddable Period?
            if (is_a($object, $entityWithEmbeddedPeriod)) {
                // Yes! Let's analyze each embeddable Period property
                foreach ($this->entitiesWithEmbeddedPeriod[$entityWithEmbeddedPeriod] as $property) {
                    $embeddablePeriod = $this->propertyAccessor->getValue($object, $property);
                    // Let's check if this embeddablePeriod should be null
                    if ($this->propertyAccessor->isUninitialized($embeddablePeriod, 'boundaryType')) {
                        // "boundaryType" property is not inizialized.
                        // This means this embeddable Period should be NULL
                        $this->propertyAccessor->setValue($object, $property, null);
                    }
                }
            }
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();
        if (null === $classMetadata->reflClass && ! $classMetadata->isMappedSuperclass) {
            return;
        }

        // Let's keep track of embedded Period entities
        foreach ($classMetadata->embeddedClasses as $propertyName => $config) {
            if ($config['class'] === Period::class) {
                $entityName = $classMetadata->getName();
                if (! isset($this->entitiesWithEmbeddedPeriod[$entityName])) {
                    $this->entitiesWithEmbeddedPeriod[$entityName] = [];
                }
                if (! in_array($propertyName, $this->entitiesWithEmbeddedPeriod[$entityName], true)) {
                    $this->entitiesWithEmbeddedPeriod[$entityName][] = $propertyName;
                }
            }
        }

        if ($classMetadata->reflClass->getName() === Period::class) {
            // TODO: permettere mapping dinamico in base alla configurazione?
        }
    }
}
