<?php

namespace DependencyInjection;

use Ermeo\RateLimitBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @var Processor
     */
    private $processor;

    public function setUp(): void
    {
        $this->processor = new Processor();
    }

    private function getConfigs(array $configArray)
    {
        $configuration = new Configuration();

        return $this->processor->processConfiguration($configuration, array($configArray));
    }

    public function testGetConfigTreeBuilder(): void
    {
        $configuration = $this->getConfigs(array());

        $this->assertSame(array(), $configuration);
    }
}
