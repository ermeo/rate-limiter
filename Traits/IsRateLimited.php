<?php

namespace Ermeo\RateLimitBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ermeo\RateLimitBundle\Entity\Doctrine\RateLimit;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;

trait IsRateLimited
{
    /**
     * @var RateLimit|null
     *
     * @ORM\ManyToOne(targetEntity="Ermeo\RateLimitBundle\Entity\Doctrine\RateLimit")
     * @ORM\JoinColumn(name="rate_limit_id", referencedColumnName="id")
     */
    private $rateLimit;

    /**
     * @return ConfigurationInterface|null
     */
    public function getRateLimit(): ?ConfigurationInterface
    {
        return $this->rateLimit;
    }

    /**
     * @param ConfigurationInterface $rateLimit
     */
    public function setRateLimit(ConfigurationInterface $rateLimit): void
    {
        $this->rateLimit = $rateLimit;
    }
}
