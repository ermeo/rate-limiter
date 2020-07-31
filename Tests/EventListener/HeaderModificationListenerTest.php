<?php

namespace Ermeo\RateLimitBundle\Tests\EventListener;

use Ermeo\RateLimitBundle\Entity\Headers;
use Ermeo\RateLimitBundle\EventListener\HeaderModificationListener;
use Ermeo\RateLimitBundle\Service\RateLimitInfo;
use Ermeo\RateLimitBundle\Service\RateLimitService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HeaderModificationListenerTest extends TestCase
{
    public function testItShouldDoNothingWhenNoProvidersIsDetected()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $headerModificationListener = new HeaderModificationListener([]);
        $headerModificationListener->onKernelResponse($event);

        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Remaining'));
        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Reset'));
    }

    public function testItShouldNotDisplayNothingWhenWeDontRetrieveRateLimitInfo()
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $rateLimitService = $this->getMockBuilder(RateLimitService::class)->disableOriginalConstructor()->getMock();

        $headerModificationListener = new HeaderModificationListener([$rateLimitService]);
        $headerModificationListener->onKernelResponse($event);

        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Remaining'));
        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Reset'));
    }

    public function testItShouldDisplayHeadersWhenHeadersIsEnabled()
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $rateLimitService = $this->getMockBuilder(RateLimitService::class)->disableOriginalConstructor()->getMock();
        $attributes = new ParameterBag();
        $headers = new Headers(true);
        $rateLimitInfo = new RateLimitInfo();
        $rateLimitInfo->setCalls(1)
            ->setLimit(10)
            ->setResetTimeStamp(12354612);
        $attributes->set('user.test', $rateLimitInfo);
        $request->attributes = $attributes;

        $rateLimitService->method('getHeaders')->willReturn($headers);
        $rateLimitService->expects($this->once())->method('getHeaderKey')->willReturn('user.test');

        $headerModificationListener = new HeaderModificationListener([$rateLimitService]);
        $headerModificationListener->onKernelResponse($event);

        $this->assertTrue($event->getResponse()->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($event->getResponse()->headers->has('X-RateLimit-Remaining'));
        $this->assertTrue($event->getResponse()->headers->has('X-RateLimit-Reset'));
        $this->assertSame('10', $event->getResponse()->headers->get('X-RateLimit-Limit'));
        $this->assertSame('9', $event->getResponse()->headers->get('X-RateLimit-Remaining'));
        $this->assertSame('12354612', $event->getResponse()->headers->get('X-RateLimit-Reset'));
    }

    public function testItShouldNotDisplayHeadersWhenHeadersNotIsEnabled()
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $rateLimitService = $this->getMockBuilder(RateLimitService::class)->disableOriginalConstructor()->getMock();
        $attributes = new ParameterBag();
        $headers = new Headers();
        $rateLimitInfo = new RateLimitInfo();
        $rateLimitInfo->setCalls(1)
            ->setLimit(10)
            ->setResetTimeStamp(12354612);
        $attributes->set('user.test', $rateLimitInfo);
        $request->attributes = $attributes;

        $rateLimitService->method('getHeaders')->willReturn($headers);
        $rateLimitService->expects($this->once())->method('getHeaderKey')->willReturn('user.test');

        $headerModificationListener = new HeaderModificationListener([$rateLimitService]);
        $headerModificationListener->onKernelResponse($event);

        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Remaining'));
        $this->assertFalse($event->getResponse()->headers->has('X-RateLimit-Reset'));
    }
}
