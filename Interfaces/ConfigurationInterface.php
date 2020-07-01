<?php

namespace Ermeo\RateLimitBundle\Interfaces;

interface ConfigurationInterface
{
    const LIMIT = 'limit';

    const PERIOD = 'period';

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return int
     */
    public function getPeriod(): int;
}
