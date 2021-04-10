<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\DependencyInjection\Compiler;

use Andante\PeriodBundle\Doctrine\EventSubscriber\PeriodEventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineEventSubscriberPass implements CompilerPassInterface
{
    public const PERIOD_SUBSCRIBER_SERVICE_ID = 'andante_period.doctrine.period_subscriber';

    public function process(ContainerBuilder $container): void
    {
        $container
            ->register(
                self::PERIOD_SUBSCRIBER_SERVICE_ID,
                PeriodEventSubscriber::class
            )
            ->addArgument(new Reference('andante_period.doctrine.embedded_period.configuration'))
            ->addTag('doctrine.event_subscriber');
    }
}
