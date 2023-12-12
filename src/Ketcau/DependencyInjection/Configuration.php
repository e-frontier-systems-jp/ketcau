<?php

namespace Ketcau\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ketcau');
        $rootNode = $treeBuilder->getRootNode();

        $this->addRateLimiterSection($rootNode);

        return $treeBuilder;
    }


    public function addRateLimiterSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('rate_limiter')
                    ->beforeNormalization()
                        ->ifTrue(fn ($v) => \is_array($v) && !isset($v['limiters']) && !isset($v['limiter']))
                        ->then(fn (array $v) => ['limiters' => $v])
                    ->end()
                    ->children()
                        ->arrayNode('limiters')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('route')
                                        ->defaultNull()
                                    ->end()
                                    ->integerNode('limit')
                                        ->isRequired()
                                    ->end()
                                    ->scalarNode('interval')
                                        ->isRequired()
                                    ->end()
                                    ->arrayNode('type')
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(fn (string $v) => [$v])
                                        ->end()
                                        ->beforeNormalization()
                                            ->ifArray()
                                            ->then(fn (array $v) => \array_map(fn ($method) => \strtolower($method), $v))
                                        ->end()
                                    ->enumPrototype()->values(['ip', 'customer', 'user'])->end()
                                        ->defaultValue([])
                                    ->end()
                                    ->arrayNode('method')
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(fn (string $v) => [$v])
                                        ->end()
                                        ->beforeNormalization()
                                            ->ifArray()
                                            ->then(fn (array $v) => \array_map(fn ($method) => \strtoupper($method), $v))
                                        ->end()
                                    ->enumPrototype()->values(['GET', 'POST', 'PUT', 'DELETE'])->end()
                                        ->defaultValue(['POST'])
                                    ->end()
                                    ->arrayNode('params')
                                        ->scalarPrototype()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}