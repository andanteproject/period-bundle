<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Functional;

use Andante\PeriodBundle\DependencyInjection\Compiler\DoctrineEventSubscriberPass;
use Andante\PeriodBundle\Doctrine\DBAL\Type\DurationType;
use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Andante\PeriodBundle\Doctrine\DBAL\Type\SequenceType;
use Andante\PeriodBundle\Doctrine\EventSubscriber\PeriodEventSubscriber;
use Andante\PeriodBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SetupTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testDoctrineTypeSetup(): void
    {
        /** @var array $types */
        $types = self::$container->getParameter('doctrine.dbal.connection_factory.types');

        self::assertArrayHasKey(DurationType::NAME, $types);
        self::assertSame(DurationType::class, $types[DurationType::NAME]['class']);

        self::assertArrayHasKey(PeriodType::NAME, $types);
        self::assertSame(PeriodType::class, $types[PeriodType::NAME]['class']);

        self::assertArrayHasKey(SequenceType::NAME, $types);
        self::assertSame(SequenceType::class, $types[SequenceType::NAME]['class']);
    }

    public function testSubscriberSetup(): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::$container->get('doctrine');
        /** @var EntityManagerInterface $em */
        foreach ($managerRegistry->getManagers() as $em) {
            $evm = $em->getEventManager();
            $r = new \ReflectionProperty($evm, 'subscribers');
            $r->setAccessible(true);
            $subscribers = $r->getValue($evm);
            $serviceIdRegistered = \in_array(
                DoctrineEventSubscriberPass::PERIOD_SUBSCRIBER_SERVICE_ID,
                $subscribers,
                true
            );
            $serviceRegistered = \array_reduce($subscribers, static fn (
                bool $carry,
                $service
            ) => $carry ? $carry : $service instanceof PeriodEventSubscriber, false);
            self::assertTrue($serviceIdRegistered || $serviceRegistered);
        }
    }
}
