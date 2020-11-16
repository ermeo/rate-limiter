<?php

namespace Ermeo\RateLimitBundle\Service;

use Ermeo\RateLimitBundle\Entity\Headers;
use Ermeo\RateLimitBundle\Exception\RateLimitExceedException;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;
use Ermeo\RateLimitBundle\Interfaces\RateLimitProviderInterface;
use Ermeo\RateLimitBundle\Interfaces\StorageInterface;
use Symfony\Component\HttpFoundation\Request;

class RateLimitService
{
    private $provider;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var StorageInterface
     */
    private $cache;

    /**
     * @var Headers
     */
    private $headers;

    /**
     * @var RateLimitExceedException
     */
    private $exception;

    /**
     * RateLimitService constructor.
     *
     * @param RateLimitProviderInterface $provider
     * @param StorageInterface           $cache
     * @param Headers                    $headers
     * @param RateLimitExceedException   $exception
     * @param string                     $prefix
     */
    public function __construct(
        RateLimitProviderInterface $provider,
        StorageInterface $cache,
        Headers $headers,
        RateLimitExceedException $exception,
        string $prefix
    ) {
        $this->prefix = $prefix;
        $this->provider = $provider;
        $this->cache = $cache;
        $this->headers = $headers;
        $this->exception = $exception;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isRateLimitExceeded(Request $request): bool
    {
        $key = $this->getCacheKey($request);
        $rateLimit = $this->getConfiguration();

        // No matching annotation found
        if (!$rateLimit) {
            return false;
        }

        // Ratelimit the call
        $rateLimitInfo = $this->cache->limitRate($key);
        if (!$rateLimitInfo) {
            // Create new rate limit entry for this call
            $rateLimitInfo = $this->cache->createRate($key, $rateLimit->getLimit(), $rateLimit->getPeriod());
            if (!$rateLimitInfo) {
                // @codeCoverageIgnoreStart
                return false;
                // @codeCoverageIgnoreEnd
            }
        }

        // Store the current rating info in the request attributes
        $request->attributes->set($this->getHeaderKey(), $rateLimitInfo);

        // Reset the rate limits
        if (time() >= $rateLimitInfo->getResetTimestamp()) {
            $this->cache->resetRate($key);
            $rateLimitInfo = $this->cache->createRate($key, $rateLimit->getLimit(), $rateLimit->getPeriod());
            if (!$rateLimitInfo) {
                // @codeCoverageIgnoreStart
                return false;
                // @codeCoverageIgnoreEnd
            }
        }

        return $rateLimitInfo->getCalls() > $rateLimitInfo->getLimit();
    }

    /**
     * @return ConfigurationInterface|null
     */
    public function getConfiguration(): ?ConfigurationInterface
    {
        return $this->provider->getRateLimit();
    }

    /**
     * @return string
     */
    public function getHeaderKey(): string
    {
        return RequestInfo::RATE_LIMIT_INFO.'.'.$this->prefix;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getCacheKey(Request $request): string
    {
        $providerKey = $this->provider->getIdentifier();

        if (null === $providerKey) {
            $providerKey = $request->getClientIp();
        }

        return 'ermeo_rate_limit.'.sha1($this->prefix.'.'.$providerKey);
    }

    /**
     * @return Headers
     */
    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    /**
     * @return RateLimitExceedException
     */
    public function getException(): RateLimitExceedException
    {
        return $this->exception;
    }
}
