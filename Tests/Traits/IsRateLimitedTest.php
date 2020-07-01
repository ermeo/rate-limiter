<?php

namespace Traits;

use Ermeo\RateLimitBundle\Configuration\ArrayConfiguration;
use Ermeo\RateLimitBundle\Traits\IsRateLimited;
use PHPUnit\Framework\TestCase;

class IsRateLimitedTest extends TestCase
{
    public function testItReturnNullWhenNoRateLimitIsFound()
    {
        $behavior = $this->getObjectForTrait(IsRateLimited::class);
        $this->assertNull($behavior->getRateLimit());
    }

    public function testItReturnRateLimit()
    {
        $behavior = $this->getObjectForTrait(IsRateLimited::class);
        $rateLimit = new ArrayConfiguration(['limit' => 10, 'period' => 60]);
        $behavior->setRateLimit($rateLimit);
        $this->assertSame($rateLimit, $behavior->getRateLimit());
    }
}
