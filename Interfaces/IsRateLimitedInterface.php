<?php

namespace Ermeo\RateLimitBundle\Interfaces;

interface IsRateLimitedInterface
{
    /**
     * @return ConfigurationInterface|null
     */
    public function getRateLimit(): ?ConfigurationInterface;

    public function setRateLimit(ConfigurationInterface $rateLimit): void;
}
