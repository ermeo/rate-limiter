<?php

namespace Ermeo\RateLimitBundle\Tests\Fixtures\Rules;

use Ermeo\RateLimitBundle\Interfaces\CheckerInterface;

class UserRules implements CheckerInterface
{
    public function byPass(): bool
    {
        return true;
    }
}
