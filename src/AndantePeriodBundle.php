<?php

declare(strict_types=1);

namespace Andante\PeriodBundle;

use Andante\PeriodBundle\DependencyInjection\Compiler\DoctrineEventSubscriberPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AndantePeriodBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineEventSubscriberPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
    }
}
