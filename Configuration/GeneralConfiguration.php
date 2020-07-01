<?php

namespace Ermeo\RateLimitBundle\Configuration;

use Ermeo\RateLimitBundle\Interfaces\CheckerInterface;

class GeneralConfiguration
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var CheckerInterface[]
     */
    private $rules;

    /**
     * GeneralConfiguration constructor.
     *
     * @param bool               $enabled
     * @param CheckerInterface[] $rules
     */
    public function __construct(bool $enabled, array $rules)
    {
        $this->enabled = $enabled;
        $this->rules = $rules;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return CheckerInterface[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
