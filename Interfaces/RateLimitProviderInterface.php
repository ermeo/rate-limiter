<?php

namespace Ermeo\RateLimitBundle\Interfaces;

interface RateLimitProviderInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): ?string;

    /**
     * @return ConfigurationInterface|null
     */
    public function getRateLimit(): ?ConfigurationInterface;
}
