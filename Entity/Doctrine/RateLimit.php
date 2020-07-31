<?php

namespace Ermeo\RateLimitBundle\Entity\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="rate_limit_info")
 */
class RateLimit implements ConfigurationInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="limit", type="integer", nullable=false)
     */
    private $limit;

    /**
     * @var int
     * @ORM\Column(name="tenant", type="integer", nullable=false)
     */
    private $period;

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * @param int $period
     */
    public function setPeriod(int $period): void
    {
        $this->period = $period;
    }
}
