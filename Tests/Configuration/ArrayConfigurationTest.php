<?php

namespace Ermeo\RateLimitBundle\Tests\Configuration;

use Ermeo\RateLimitBundle\Configuration\ArrayConfiguration;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

class ArrayConfigurationTest extends TestCase
{
    public function testItShouldReturnconfigurationValue()
    {
        $configuration = new ArrayConfiguration([
            ConfigurationInterface::LIMIT => 1,
            ConfigurationInterface::PERIOD => 60,
        ]);

        $this->assertSame(1, $configuration->getLimit());
        $this->assertSame(60, $configuration->getPeriod());
    }
}
