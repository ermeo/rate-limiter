<?php

namespace Ermeo\RateLimitBundle\Tests\Cache;

use Ermeo\RateLimitBundle\Cache\Redis;
use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    public function testgetRateInfo()
    {
        $client = $this->getMockBuilder('Predis\\Client')
            ->setMethods(['hgetall'])
            ->getMock();
        $client->expects($this->once())
            ->method('hgetall')
            ->with('foo')
            ->will($this->returnValue(['limit' => 100, 'calls' => 50, 'reset' => 1234]));

        $storage = new Redis($client);
        $rli = $storage->getRateInfo('foo');
        $this->assertInstanceOf('Ermeo\\RateLimitBundle\\Service\\RateLimitInfo', $rli);
        $this->assertEquals(100, $rli->getLimit());
        $this->assertEquals(50, $rli->getCalls());
        $this->assertEquals(1234, $rli->getResetTimestamp());
    }

    public function testcreateRate()
    {
        $client = $this->getMockBuilder('Predis\\Client')
            ->setMethods(['hset', 'expire', 'hgetall'])
            ->getMock();
        $client->expects($this->once())
            ->method('expire')
            ->with('foo', 123);
        $client->expects($this->exactly(3))
            ->method('hset')
            ->withConsecutive(
                ['foo', 'limit', 100],
                ['foo', 'calls', 1],
                ['foo', 'reset']
            );

        $storage = new Redis($client);
        $storage->createRate('foo', 100, 123);
    }

    public function testLimitRateNoKey()
    {
        $client = $this->getMockBuilder('Predis\\Client')
            ->setMethods(['hgetall'])
            ->getMock();
        $client->expects($this->once())
            ->method('hgetall')
            ->with('foo')
            ->will($this->returnValue([]));

        $storage = new Redis($client);
        $this->assertFalse($storage->limitRate('foo'));
    }

    public function testLimitRateWithKey()
    {
        $client = $this->getMockBuilder('Predis\\Client')
            ->setMethods(['hexists', 'hincrby', 'hgetall'])
            ->getMock();
        $client->expects($this->once())
            ->method('hgetall')
            ->with('foo')
            ->will(
                $this->returnValue(
                    [
                        'limit' => 1,
                        'calls' => 1,
                        'reset' => 1,
                    ]
                )
            );
        $client->expects($this->once())
            ->method('hincrby')
            ->with('foo', 'calls', 1)
            ->will($this->returnValue(2));

        $storage = new Redis($client);
        $storage->limitRate('foo');
    }

    public function testresetRate()
    {
        $client = $this->getMockBuilder('Predis\\Client')
            ->setMethods(['del'])
            ->getMock();
        $client->expects($this->once())
            ->method('del')
            ->with('foo');

        $storage = new Redis($client);
        $this->assertTrue($storage->resetRate('foo'));
    }
}
