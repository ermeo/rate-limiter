<?php

namespace Ermeo\RateLimitBundle\Tests\Service;

use Ermeo\RateLimitBundle\Service\RateLimitInfo;
use PHPUnit\Framework\TestCase;
use TypeError;

class RateLimitInfoTest extends TestCase
{
    /**
     * @var RateLimitInfo
     */
    private $rateLimitInfo;

    public function setUp(): void
    {
        $this->rateLimitInfo = new RateLimitInfo();
    }

    public function testGetCalls()
    {
        $this->rateLimitInfo->setCalls(10);

        $calls = $this->rateLimitInfo->getCalls();
        $this->assertSame(10, $calls);
        $this->assertIsInt($calls);
    }

    public function testGetResetTimeStamp()
    {
        $this->rateLimitInfo->setResetTimeStamp(10);

        $resetTimeStamp = $this->rateLimitInfo->getResetTimeStamp();
        $this->assertSame(10, $resetTimeStamp);
        $this->assertIsInt($resetTimeStamp);
    }

    public function testSetLimitWithParameterNotInt()
    {
        $this->expectException(TypeError::class);
        $this->rateLimitInfo->setLimit('salut');
    }

    public function testGetLimit()
    {
        $this->rateLimitInfo->setLimit(10);

        $limit = $this->rateLimitInfo->getLimit();
        $this->assertSame(10, $limit);
        $this->assertIsInt($limit);
    }

    public function testSetCallsFailsWithParameterNotInt()
    {
        $this->expectException(TypeError::class);
        $this->rateLimitInfo->setCalls('salut');
    }

    public function testSetResetTimeStampFailsWithParameterNotInt()
    {
        $this->expectException(TypeError::class);
        $this->rateLimitInfo->setResetTimeStamp('salut');
    }
}
