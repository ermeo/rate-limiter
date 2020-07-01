<?php

namespace Ermeo\RateLimitBundle\Entity;

class Headers
{
    private $enabled;

    private $limit;

    private $remaining;

    private $reset;

    public function __construct(
        bool $enabled = false,
        string $limit = 'X-RateLimit-Limit',
        string $remaining = 'X-RateLimit-Remaining',
        string $reset = 'X-RateLimit-Reset'
    ) {
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->reset = $reset;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getRemaining(): string
    {
        return $this->remaining;
    }

    /**
     * @return string
     */
    public function getReset(): string
    {
        return $this->reset;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
