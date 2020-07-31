<?php

namespace Ermeo\RateLimitBundle\Tests\Entity;

use Ermeo\RateLimitBundle\Entity\Headers;
use PHPUnit\Framework\TestCase;

class HeadersTest extends TestCase
{
    public function testItShouldReturnDefaultValue()
    {
        $headers = new Headers();

        $this->assertFalse($headers->isEnabled());
        $this->assertSame('X-RateLimit-Limit', $headers->getLimit());
        $this->assertSame('X-RateLimit-Reset', $headers->getReset());
        $this->assertSame('X-RateLimit-Remaining', $headers->getRemaining());
    }

    public function testItShouldReturnValueSetOnConstructor()
    {
        $headers = new Headers(true, 'foo', 'bar', 'FooBar');

        $this->assertTrue($headers->isEnabled());
        $this->assertSame('foo', $headers->getLimit());
        $this->assertSame('bar', $headers->getRemaining());
        $this->assertSame('FooBar', $headers->getReset());
    }
}
