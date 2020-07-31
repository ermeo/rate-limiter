<?php

namespace Ermeo\RateLimitBundle\Tests\DependencyInjection;

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

    private function getConfigs(array $configArray): array
    {
        $configuration = new Configuration();

        return $this->processor->processConfiguration($configuration, [$configArray]);
    }

    public function testGetConfigTreeBuilder(): void
    {
        $configuration = $this->getConfigs([]);

        $this->assertSame(['enabled' => true, 'rules' => [], 'providers' => []], $configuration);
    }
}
