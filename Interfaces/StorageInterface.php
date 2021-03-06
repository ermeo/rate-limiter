<?php

namespace Ermeo\RateLimitBundle\Interfaces;

use Ermeo\RateLimitBundle\Service\RateLimitInfo;

interface StorageInterface
{
    /**
     * Get information about the current rate.
     *
     * @param string $key
     *
     * @return RateLimitInfo Rate limit information
     */
    public function getRateInfo($key);

    /**
     * Limit the rate by one.
     *
     * @param string $key
     *
     * @return RateLimitInfo Rate limit info
     */
    public function limitRate($key);

    /**
     * Create a new rate entry.
     *
     * @param string $key
     * @param int    $limit
     * @param int    $period
     */
    public function createRate($key, $limit, $period);

    /**
     * Reset the rating.
     *
     * @param $key
     */
    public function resetRate($key);
}
