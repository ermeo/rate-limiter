<?php

namespace Ermeo\RateLimitBundle\Tests\Fixtures\Providers;

use Ermeo\RateLimitBundle\Configuration\ArrayConfiguration;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;
use Ermeo\RateLimitBundle\Providers\AbstractRateLimitProvider;

class ClientProvider extends AbstractRateLimitProvider
{
    public function getIdentifier(): string
    {
        return 'client';
    }

    /**
     * @return ConfigurationInterface
     */
    public function getRateLimit(): ?ConfigurationInterface
    {
        return new ArrayConfiguration([ConfigurationInterface::LIMIT => 1, ConfigurationInterface::PERIOD => 60]);
    }
}
