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

    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        $classMetadata = $eventArgs->getEntityManager()->getClassMetadata(get_class($entity));
        // Let's search for embedded Period entities
        foreach ($classMetadata->embeddedClasses as $propertyName => $config) {
            if ($config['class'] === Period::class) {
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
        if (null === $classMetadata->reflClass && ! $classMetadata->isMappedSuperclass) {
            return;
        }

        if ($classMetadata->reflClass->getName() === Period::class) {
            // TODO: allow change database mapping via configuration?
        }
    }
}
