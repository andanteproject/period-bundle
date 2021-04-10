<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('andante_period');

        //@formatter:off
        /** @var ArrayNodeDefinition $node */
        $node = $treeBuilder->getRootNode();
        $node->children()
            ->arrayNode('doctrine')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('embedded_period')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('default')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('start_date_column_name')
                                        ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('end_date_column_name')
                                        ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('boundary_type_column_name')
                                        ->defaultValue(null)
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('entity')
                                ->arrayPrototype()
                                ->children()
                                    ->scalarNode('start_date_column_name')
                                        ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('end_date_column_name')
                                        ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('boundary_type_column_name')
                                        ->defaultValue(null)
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
        //@formatter:on

        return $treeBuilder;
    }
}
