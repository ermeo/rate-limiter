<?php

namespace Ermeo\RateLimitBundle\Tests\EventListener;

use Ermeo\RateLimitBundle\Configuration\GeneralConfiguration;
use Ermeo\RateLimitBundle\EventListener\RateLimitListener;
use Ermeo\RateLimitBundle\Exception\RateLimitExceedException;
use Ermeo\RateLimitBundle\Service\RateLimitService;
use Ermeo\RateLimitBundle\Tests\Fixtures\Rules\UserRules;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RateLimitListenerTest extends TestCase
{
    public function testItReturnNothingIfRequestIsNotMaster(): void
    {
        $getResponseEvent = $this->getMockBuilder(GetResponseEvent::class)->disableOriginalConstructor()->getMock();
        $getResponseEvent->expects($this->once())->method('isMasterRequest')->willReturn(false);

        $generalConfiguration = $this->getMockBuilder(GeneralConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $generalConfiguration->expects($this->never())->method('getRules');

        $listener = new RateLimitListener([], $generalConfiguration);
        $listener->onKernelRequest($getResponseEvent);
    }

    public function testItContinueIfRequestIsMasterRequest(): void
    {
        $getResponseEvent = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()->getMock();
        $getResponseEvent->expects($this->once())->method('isMasterRequest')->willReturn(true);

        $generalConfiguration = $this->getMockBuilder(GeneralConfiguration::class)
            ->disableOriginalConstructor()->getMock();
        $generalConfiguration->expects($this->once())->method('isEnabled')->willReturn(true);
        $generalConfiguration->expects($this->once())->method('getRules')->willReturn([]);

        $listener = new RateLimitListener([], $generalConfiguration);
        $listener->onKernelRequest($getResponseEvent);
    }

    public function testItShouldByPassProviderWhenRulesByPassMethodReturnTrue()
    {
        $rules = [new UserRules()];
        $request = $this->getMockBuilder(Request::class)->getMock();

        $getResponseEvent = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()->getMock();
        $getResponseEvent->expects($this->once())->method('isMasterRequest')->willReturn(true);
        $getResponseEvent->expects($this->once())->method('getRequest')->willReturn($request);

        $generalConfiguration = $this->getMockBuilder(GeneralConfiguration::class)
            ->disableOriginalConstructor()->getMock();
        $generalConfiguration->expects($this->once())->method('isEnabled')->willReturn(true);
        $generalConfiguration->expects($this->once())->method('getRules')->willReturn($rules);

        $rateLimitService = $this->getMockBuilder(RateLimitService::class)->disableOriginalConstructor()->getMock();
        $rateLimitService->expects($this->never())->method('isRateLimitExceeded')->with($request);

        $listener = new RateLimitListener([$rateLimitService], $generalConfiguration);
        $listener->onKernelRequest($getResponseEvent);
    }

    public function testItShouldSetResponseWhenRateLimitExceed()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $getResponseEvent = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()->getMock();
        $getResponseEvent->expects($this->once())->method('isMasterRequest')->willReturn(true);
        $getResponseEvent->expects($this->once())->method('getRequest')->willReturn($request);

        $generalConfiguration = $this->getMockBuilder(GeneralConfiguration::class)
            ->disableOriginalConstructor()->getMock();
        $generalConfiguration->expects($this->once())->method('isEnabled')->willReturn(true);
        $rateLimitException = new RateLimitExceedException(429, 'Too Many Request');

        $rateLimitService = $this->getMockBuilder(RateLimitService::class)->disableOriginalConstructor()->getMock();
        $rateLimitService->method('getException')->willReturn($rateLimitException);
        $rateLimitService->expects($this->once())->method('isRateLimitExceeded')->with($request)->willReturn(true);
        $getResponseEvent->expects($this->once())->method('setResponse');

        $listener = new RateLimitListener([$rateLimitService], $generalConfiguration);
        $listener->onKernelRequest($getResponseEvent);
    }
}
