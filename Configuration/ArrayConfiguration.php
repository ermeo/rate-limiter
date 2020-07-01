<?php

namespace Ermeo\RateLimitBundle\Configuration;

use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;

class ArrayConfiguration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * ArrayStrategy constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->config[self::LIMIT];
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->config[self::PERIOD];
    }
}
