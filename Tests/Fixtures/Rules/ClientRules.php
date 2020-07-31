<?php

namespace Ermeo\RateLimitBundle\Tests\Fixtures\Rules;

use Ermeo\RateLimitBundle\Interfaces\CheckerInterface;

class ClientRules implements CheckerInterface
{
    public function byPass(): bool
    {
        return false;
    }
}
