<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests;

use Andante\PeriodBundle\Tests\HttpKernel\AndantePeriodKernel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AndantePeriodKernel::class;
    }

    protected function createSchema(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine.orm.default_entity_manager');

        /** @var array<int, ClassMetadata> $allMetadata */
        $allMetadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($allMetadata);
        $schemaTool->createSchema($allMetadata);
    }
}
