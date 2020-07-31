<?php

namespace Ermeo\RateLimitBundle\Tests\Fixtures\Providers;

use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;
use Ermeo\RateLimitBundle\Providers\AbstractRateLimitProvider;

class UserProvider extends AbstractRateLimitProvider
{
    public function getIdentifier(): ?string
    {
        return null;
    }

    /**
     * @return ConfigurationInterface|null
     */
    public function getRateLimit(): ?ConfigurationInterface
    {
        return null;
    }
}
