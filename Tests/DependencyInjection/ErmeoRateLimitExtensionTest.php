<?php

namespace Ermeo\RateLimitBundle\Tests\DependencyInjection;

use Ermeo\RateLimitBundle\DependencyInjection\ErmeoRateLimitExtension;
use Ermeo\RateLimitBundle\Tests\Fixtures\Rules\UserRules;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ErmeoRateLimitExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var ErmeoRateLimitExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new ErmeoRateLimitExtension();
    }

    public function testEventListenerConfigWithRedisCache()
    {
        $config = [
            [
                'enabled' => false,
                'cache' => [
                    'storage_engine' => 'redis',
                    'provider' => 'default',
                ],
                'rules' => [
                    'OAuth' => [
                        'service' => 'test',
                    ],
                ],
                'providers' => [
                    'user' => [
                        'service' => 'test',
                    ],
                ],
            ],
        ];
        $this->container->setDefinition('test', new Definition(UserRules::class));
        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.rate_limit'));
        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.header_modification'));
    }

    public function testEventListenerConfigWithPhpRedisCache()
    {
        $config = [
            [
                'enabled' => false,
                'cache' => [
                    'storage_engine' => 'php_redis',
                    'provider' => 'default',
                ],
                'rules' => [
                    'OAuth' => [
                        'service' => 'test',
                    ],
                ],
                'providers' => [
                    'user' => [
                        'service' => 'test',
                    ],
                ],
            ],
        ];
        $this->container->setDefinition('test', new Definition(UserRules::class));
        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.rate_limit'));
        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.header_modification'));
    }

    public function testItShouldntLoadRulesWhenRulesIsEmpty()
    {
        $config = [
            [
                'enabled' => false,
                'cache' => [
                    'storage_engine' => 'redis',
                    'provider' => 'default',
                ],
                'providers' => [
                    'user' => [
                        'service' => 'test',
                    ],
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $this->assertSame([], $this->container->getDefinition('ermeo_rate_limit.configuration.general')->getArgument(1));
        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.rate_limit'));
        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.header_modification'));
    }

    public function testItShouldntLoadRulesWhenRulesIsEmptyT()
    {
        $config = [
            [
                'enabled' => false,
                'cache' => [
                    'storage_engine' => 'redis',
                    'provider' => 'default',
                ],
                'rules' => [
                    'OAuth' => [
                        'service' => 'test',
                    ],
                ],
                'providers' => [
                    'user' => [
                        'service' => 'test',
                    ],
                ],
            ],
        ];
        $userRules = new Definition(UserRules::class);
        $this->container->setDefinition('test', $userRules);
        $this->extension->load($config, $this->container);

        $this->assertSame([$userRules], $this->container->getDefinition('ermeo_rate_limit.configuration.general')->getArgument(1));
        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.rate_limit'));
        $this->assertTrue($this->container->hasDefinition('ermeo_rate_limit.event_listener.header_modification'));
    }
}
