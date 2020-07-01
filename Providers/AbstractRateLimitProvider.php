<?php

namespace Ermeo\RateLimitBundle\Providers;

use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;
use Ermeo\RateLimitBundle\Interfaces\RateLimitProviderInterface;

abstract class AbstractRateLimitProvider implements RateLimitProviderInterface
{
    /**
     * @return string
     */
    abstract public function getIdentifier(): ?string;

    /**
     * @return ConfigurationInterface|null
     */
    abstract public function getRateLimit(): ?ConfigurationInterface;
}
