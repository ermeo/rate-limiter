<?php

namespace Ermeo\RateLimitBundle\Service;

class RateLimitInfo
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $calls;

    /**
     * @var int
     */
    private $resetTimeStamp;

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return RateLimitInfo
     */
    public function setLimit(int $limit): RateLimitInfo
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getCalls(): int
    {
        return $this->calls;
    }

    /**
     * @param int $calls
     *
     * @return RateLimitInfo
     */
    public function setCalls(int $calls): RateLimitInfo
    {
        $this->calls = $calls;

        return $this;
    }

    /**
     * @return int
     */
    public function getResetTimeStamp(): int
    {
        return $this->resetTimeStamp;
    }

    /**
     * @param int $resetTimeStamp
     *
     * @return RateLimitInfo
     */
    public function setResetTimeStamp(int $resetTimeStamp): RateLimitInfo
    {
        $this->resetTimeStamp = $resetTimeStamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getRemaining(): int
    {
        return $this->limit - $this->calls;
    }
}
