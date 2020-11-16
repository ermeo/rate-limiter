<?php

namespace Ermeo\RateLimitBundle\Tests\Service;

use DateTime;
use Ermeo\RateLimitBundle\Cache\Redis;
use Ermeo\RateLimitBundle\Entity\Headers;
use Ermeo\RateLimitBundle\Exception\RateLimitExceedException;
use Ermeo\RateLimitBundle\Interfaces\ConfigurationInterface;
use Ermeo\RateLimitBundle\Service\RateLimitInfo;
use Ermeo\RateLimitBundle\Service\RateLimitService;
use Ermeo\RateLimitBundle\Service\RequestInfo;
use Ermeo\RateLimitBundle\Tests\Fixtures\Providers\ClientProvider;
use Ermeo\RateLimitBundle\Tests\Fixtures\Providers\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RateLimitServiceTest extends TestCase
{
    /**
     * @var RateLimitService
     */
    private $rateLimitService;

    /**
     * @var ClientProvider
     */
    private $clientProvider;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cache;

    /**
     * @var Headers
     */
    private $headers;

    /**
     * @var RateLimitExceedException
     */
    private $exception;

    public function setUp(): void
    {
        parent::setUp();
        $this->clientProvider = new ClientProvider();
        $this->cache = $this->getMockBuilder(Redis::class)->disableOriginalConstructor()->getMock();
        $this->headers = new Headers();
        $this->exception = new RateLimitExceedException(429, 'limit exceed');
        $this->prefix = 'client';
        $this->rateLimitService = new RateLimitService(
            $this->clientProvider,
            $this->cache,
            $this->headers,
            $this->exception,
            $this->prefix
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->clientProvider);
        unset($this->cache);
        unset($this->headers);
        unset($this->exception);
        unset($this->prefix);
        unset($this->rateLimitService);
    }

    public function testItShouldReturnConfigurationInterface()
    {
        $this->assertInstanceOf(ConfigurationInterface::class, $this->rateLimitService->getConfiguration());
    }

    public function testItShouldReturnTheCorrectCacheKey()
    {
        $request = new Request();
        $expected = 'ermeo_rate_limit.'.sha1($this->prefix.'.'.$this->clientProvider->getIdentifier());

        $this->assertSame($expected, $this->rateLimitService->getCacheKey($request));
    }

    public function testItShouldReturnTheCorrectCacheKeyWithoutIdentifier()
    {
        $request = new Request();
        $clientProvider = new UserProvider();
        $cache = $this->getMockBuilder(Redis::class)->disableOriginalConstructor()->getMock();
        $headers = new Headers();
        $exception = new RateLimitExceedException(429, 'limit exceed');
        $prefix = 'client';
        $rateLimitService = new RateLimitService(
            $clientProvider,
            $cache,
            $headers,
            $exception,
            $prefix
        );
        $expected = 'ermeo_rate_limit.'.sha1($this->prefix.'.'.$clientProvider->getIdentifier());

        $this->assertSame($expected, $rateLimitService->getCacheKey($request));
    }

    public function testItShouldReturnHeaders()
    {
        $this->assertInstanceOf(Headers::class, $this->rateLimitService->getHeaders());
    }

    public function testItShouldReturnHeadersKey()
    {
        $headerKey = RequestInfo::RATE_LIMIT_INFO.'.'.$this->prefix;
        $this->assertSame($headerKey, $this->rateLimitService->getHeaderKey());
    }

    public function testItShouldReturnException()
    {
        $this->assertInstanceOf(RateLimitExceedException::class, $this->rateLimitService->getException());
    }

    public function testItShouldReturnFalseWhenWeDontFoundRateLimitConfiguration()
    {
        $ratelimitService = new RateLimitService(
            new UserProvider(),
            $this->cache,
            $this->headers,
            $this->exception,
            'user'
        );
        $request = new Request();
        $this->assertFalse($ratelimitService->isRateLimitExceeded($request));
    }

    public function testItShouldCreateCacheRateLimitInfoWhenNoCacheKeyIsFoundOnCache()
    {
        $dateTimeInterval = new \DateInterval('P1D');
        $dateTime = (new DateTime('now'))->add($dateTimeInterval)->getTimestamp();
        $rateLimitInfo = $this->getMockBuilder(RateLimitInfo::class)->getMock();
        $rateLimitInfo->expects($this->once())->method('getResetTimestamp')->willReturn($dateTime);
        $request = new Request();
        $this->cache->expects($this->at(0))->method('limitRate')->willReturn(null);
        $this->cache->expects($this->at(1))->method('createRate')->willReturn($rateLimitInfo);

        $exceed = $this->rateLimitService->isRateLimitExceeded($request);

        $this->assertFalse($exceed);
        $this->assertSame($rateLimitInfo, $request->attributes->get(RequestInfo::RATE_LIMIT_INFO.'.'.$this->prefix));
    }

    public function testItShouldResetCacheInfoWhenResetTimestampIsUpperThanTimeNow()
    {
        $dateTimeInterval = new \DateInterval('P1D');
        $dateTime = (new DateTime('now'))->sub($dateTimeInterval)->getTimestamp();
        $rateLimitInfo = $this->getMockBuilder(RateLimitInfo::class)->getMock();
        $rateLimitInfo->expects($this->once())->method('getResetTimestamp')->willReturn($dateTime);
        $request = new Request();
        $this->cache->expects($this->at(0))->method('limitRate')->willReturn($rateLimitInfo);
        $this->cache->expects($this->at(1))->method('resetRate');
        $this->cache->expects($this->at(2))->method('createRate')->willReturn($rateLimitInfo);

        $exceed = $this->rateLimitService->isRateLimitExceeded($request);

        $this->assertFalse($exceed);
        $this->assertSame($rateLimitInfo, $request->attributes->get(RequestInfo::RATE_LIMIT_INFO.'.'.$this->prefix));
    }

    public function testItShouldReturnTrueWhenCallsIsUpperThanLimit()
    {
        $dateTimeInterval = new \DateInterval('P1D');
        $dateTime = (new DateTime('now'))->add($dateTimeInterval)->getTimestamp();
        $rateLimitInfo = $this->getMockBuilder(RateLimitInfo::class)->getMock();
        $rateLimitInfo->expects($this->once())->method('getResetTimestamp')->willReturn($dateTime);
        $rateLimitInfo->expects($this->once())->method('getCalls')->willReturn(10);
        $rateLimitInfo->expects($this->once())->method('getLimit')->willReturn(5);
        $request = new Request();
        $this->cache->expects($this->at(0))->method('limitRate')->willReturn(null);
        $this->cache->expects($this->at(1))->method('createRate')->willReturn($rateLimitInfo);

        $exceed = $this->rateLimitService->isRateLimitExceeded($request);

        $this->assertTrue($exceed);
        $this->assertSame($rateLimitInfo, $request->attributes->get(RequestInfo::RATE_LIMIT_INFO.'.'.$this->prefix));
    }
}
