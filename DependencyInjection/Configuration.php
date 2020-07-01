<?php

namespace Ermeo\RateLimitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Response;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ermeo_rate_limit');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('cache')
                    ->children()
                        ->enumNode('storage_engine')->values(['redis', 'php_redis'])->end()
                        ->scalarNode('provider')->end()
                    ->end()
                ->end()
                ->arrayNode('rules')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')->cannotBeEmpty()->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('providers')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')->cannotBeEmpty()->isRequired()->end()
                            ->arrayNode('headers')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('display')->defaultFalse()->end()
                                    ->arrayNode('names')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('limit')->cannotBeEmpty()->defaultValue('X-RateLimit-Limit')->end()
                                            ->scalarNode('remaining')->cannotBeEmpty()->defaultValue('X-RateLimit-Remaining')->end()
                                            ->scalarNode('reset')->cannotBeEmpty()->defaultValue('X-RateLimit-Reset')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('exception')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->integerNode('code')
                                        ->defaultValue(Response::HTTP_TOO_MANY_REQUESTS)
                                        ->validate()
                                        ->ifNotInArray(array_keys(Response::$statusTexts))
                                        ->thenInvalid('Invalid status code "%s"')
                                        ->end()
                                    ->end()
                                    ->scalarNode('message')->cannotBeEmpty()->defaultValue('API rate limit exceeded.')->end()
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
