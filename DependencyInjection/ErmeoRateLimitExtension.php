<?php

namespace Ermeo\RateLimitBundle\DependencyInjection;

use Ermeo\RateLimitBundle\Cache\PhpRedis;
use Ermeo\RateLimitBundle\Cache\Redis;
use Ermeo\RateLimitBundle\Entity\Headers;
use Ermeo\RateLimitBundle\Exception\RateLimitExceedException;
use Ermeo\RateLimitBundle\Service\RateLimitService;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ErmeoRateLimitExtension extends Extension
{
    /** @var array */
    private $providers = [];

    /** @var array */
    private $rules = [];

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->loadRules($container, $config);
        $this->loadGeneralConfiguration($container, $config);
        $this->loadCache($container, $config);
        $this->loadProviders($container, $config);
        $this->registerEventListener($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function loadRules(ContainerBuilder $container, array $config): void
    {
        if (true === empty($config['rules'])) {
            return;
        }
        foreach ($config['rules'] as $key => $rule) {
            $this->rules[] = $container->getDefinition($rule['service']);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function loadGeneralConfiguration(ContainerBuilder $container, array $config): void
    {
        $container->getDefinition('ermeo_rate_limit.configuration.general')
            ->replaceArgument(
                0,
                $config['enabled']
            )->replaceArgument(1, $this->rules);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function loadCache(ContainerBuilder $container, array $config): void
    {
        switch ($config['cache']['storage_engine']) {
            case 'redis':
                $container->setParameter(
                    'ermeo_rate_limit.storage.class',
                    Redis::class
                );

                $service = $config['cache']['provider'];
                $container->getDefinition('ermeo_rate_limit.storage')->replaceArgument(
                    0,
                    new Reference($service)
                );

                break;
            case 'php_redis':
                $container->setParameter(
                    'ermeo_rate_limit.storage.class',
                    PhpRedis::class
                );
                $container->getDefinition('ermeo_rate_limit.storage')->replaceArgument(
                    0,
                    new Reference($config['cache']['provider'])
                );

                break;
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function loadProviders(ContainerBuilder $container, array $config): void
    {
        $cache = new Reference('ermeo_rate_limit.storage');

        foreach ($config['providers'] as $key => $providerConfig) {
            $providerDefinition = $this->loadProvider($providerConfig);
            $headerDefinition = $this->loadHeaders($providerConfig['headers']);
            $exception = $this->loadException($container, $providerConfig, $key);
            //load information into RateLimitService
            $definition = new Definition(
                RateLimitService::class,
                [$providerDefinition, $cache, $headerDefinition, new Reference($exception), $key]
            );
            $definition->setPublic(true);

            $name = sprintf('ermeo_rate_limit.providers.%s_provider', $key);
            $container->setDefinition($name, $definition);

            $this->providers[] = $definition;
        }
    }

    /**
     * @param array $providerConfig
     *
     * @return Reference
     */
    private function loadProvider(array $providerConfig): Reference
    {
        return new Reference($providerConfig['service']);
    }

    /**
     * @param $headers
     *
     * @return Definition
     */
    private function loadHeaders(array $headers): Definition
    {
        return new Definition(
            Headers::class,
            [
                $headers['display'],
                $headers['names']['limit'],
                $headers['names']['remaining'],
                $headers['names']['reset'],
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function registerEventListener(ContainerBuilder $container, array $config): void
    {
        $container->getDefinition('ermeo_rate_limit.event_listener.rate_limit')
            ->replaceArgument(0, $this->providers)
            ->replaceArgument(1, $container->getDefinition('ermeo_rate_limit.configuration.general'));

        $container->getDefinition('ermeo_rate_limit.event_listener.header_modification')
            ->replaceArgument(0, $this->providers);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     * @param string           $key
     *
     * @return string
     */
    private function loadException(ContainerBuilder $container, array $config, string $key): string
    {
        $exception = new Definition(
            RateLimitExceedException::class,
            [$config['exception']['code'], $config['exception']['message']]
        );
        $exception->setShared(false)
            ->setPublic(true);

        $name = sprintf('ermeo_rate_limit.exception.%s_exceed', $key);
        $container->setDefinition($name, $exception);

        return $name;
    }
}
