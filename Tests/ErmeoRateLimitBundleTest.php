<?php
namespace Ermeo\RateLimitBundle\Tests;

use Ermeo\RateLimitBundle\ErmeoRateLimitBundle;
use PHPUnit\Framework\TestCase;

class ErmeoRateLimitBundleTest extends TestCase
{
    public function testItReturnErmeoRateLimitBundle(): void
    {
        $bundle = new ErmeoRateLimitBundle();
        $this->assertInstanceOf('Ermeo\\RateLimitBundle\\ErmeoRateLimitBundle', $bundle);
    }
}
