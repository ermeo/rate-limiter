<?php

namespace Ermeo\RateLimitBundle\Cache;

use Ermeo\RateLimitBundle\Interfaces\StorageInterface;
use Ermeo\RateLimitBundle\Service\RateLimitInfo;

class PhpRedis implements StorageInterface
{
    /**
     * @var \Redis
     */
    protected $client;

    /**
     * PhpRedis constructor.
     *
     * @param \Redis $client
     */
    public function __construct(\Redis $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $key
     *
     * @return bool|RateLimitInfo
     */
    public function limitRate($key)
    {
        $info = $this->getRateInfo($key);
        if (!$info) {
            return false;
        }

        $calls = $this->client->hincrby($key, 'calls', 1);
        $info->setCalls($calls);

        return $info;
    }

    /**
     * @param string $key
     *
     * @return bool|RateLimitInfo
     */
    public function getRateInfo($key)
    {
        $info = $this->client->hgetall($key);
        if (!isset($info['limit']) || !isset($info['calls']) || !isset($info['reset'])) {
            return false;
        }

        $rateLimitInfo = new RateLimitInfo();
        $rateLimitInfo->setLimit($info['limit']);
        $rateLimitInfo->setCalls($info['calls']);
        $rateLimitInfo->setResetTimestamp($info['reset']);

        return $rateLimitInfo;
    }

    /**
     * @param string $key
     * @param int    $limit
     * @param int    $period
     *
     * @return RateLimitInfo
     */
    public function createRate($key, $limit, $period): RateLimitInfo
    {
        $reset = time() + $period;

        $this->client->hset($key, 'limit', $limit);
        $this->client->hset($key, 'calls', 1);
        $this->client->hset($key, 'reset', $reset);
        $this->client->expire($key, $period);

        $rateLimitInfo = new RateLimitInfo();
        $rateLimitInfo->setLimit($limit);
        $rateLimitInfo->setCalls(1);
        $rateLimitInfo->setResetTimestamp($reset);

        return $rateLimitInfo;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function resetRate($key): bool
    {
        $this->client->del($key);

        return true;
    }
}
