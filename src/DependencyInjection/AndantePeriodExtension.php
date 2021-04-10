<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\DependencyInjection;

use Andante\PeriodBundle\Config\Doctrine\EmbeddedPeriod\Configuration;
use Andante\PeriodBundle\DependencyInjection\Configuration as BundleConfiguration;
use Andante\PeriodBundle\Doctrine\DBAL\Type\DurationType;
use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Andante\PeriodBundle\Doctrine\DBAL\Type\SequenceType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class AndantePeriodExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container):void
    {
        $configuration = new BundleConfiguration();
        $config = $this->processConfiguration($configuration, $configs);
        $container
            ->setDefinition('andante_period.doctrine.embedded_period.configuration', new Definition(Configuration::class))
            ->setFactory([Configuration::class, 'createFromArray'])
            ->setArguments([$config['doctrine']['embedded_period']]);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'AndantePeriod' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => sprintf('%s/../Resources/config/orm', __DIR__),
                        'prefix' => 'League\Period',
                    ],
                ],
            ],
            'dbal' => [
                'types' => [
                    DurationType::NAME => DurationType::class,
                    PeriodType::NAME => PeriodType::class,
                    SequenceType::NAME => SequenceType::class,
                ],
            ],
        ]);
    }
}
