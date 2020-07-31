<?php

namespace Ermeo\RateLimitBundle\EventListener;

use Ermeo\RateLimitBundle\Service\RateLimitInfo;
use Ermeo\RateLimitBundle\Service\RateLimitService;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HeaderModificationListener
{
    /**
     * @var array
     */
    private $rateLimitProviders;

    /**
     * HeaderModificationListener constructor.
     *
     * @param array $rateLimitProviders
     */
    public function __construct(array $rateLimitProviders)
    {
        $this->rateLimitProviders = $rateLimitProviders;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        /** @var RateLimitService $rateLimitService */
        foreach ($this->rateLimitProviders as $rateLimitService) {
            /** @var RateLimitInfo|null $rateLimitInfo */
            $rateLimitInfo = $request->attributes->get($rateLimitService->getHeaderKey(), null);
            if (!$rateLimitInfo) {
                continue;
            }

            $headers = $rateLimitService->getHeaders();

            if ($headers->isEnabled()) {
                $response->headers->set($headers->getLimit(), $rateLimitInfo->getLimit());
                $response->headers->set($headers->getRemaining(), $rateLimitInfo->getRemaining());
                $response->headers->set($headers->getReset(), $rateLimitInfo->getResetTimeStamp());
            }
        }
    }
}
