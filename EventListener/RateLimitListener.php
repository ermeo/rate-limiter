<?php

namespace Ermeo\RateLimitBundle\EventListener;

use Ermeo\RateLimitBundle\Configuration\GeneralConfiguration;
use Ermeo\RateLimitBundle\Interfaces\CheckerInterface;
use Ermeo\RateLimitBundle\Service\RateLimitService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RateLimitListener
{
    /**
     * @var array
     */
    private $rateLimitProviders;

    /**
     * @var GeneralConfiguration
     */
    private $generalConfiguration;

    /**
     * RateLimitListener constructor.
     *
     * @param array                $rateLimitProviders
     * @param GeneralConfiguration $generalConfiguration
     */
    public function __construct(array $rateLimitProviders, GeneralConfiguration $generalConfiguration)
    {
        $this->rateLimitProviders = $rateLimitProviders;
        $this->generalConfiguration = $generalConfiguration;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || !$this->generalConfiguration->isEnabled()) {
            // don't do anything if it's not the master request
            return;
        }

        /** @var CheckerInterface $rule */
        foreach ($this->generalConfiguration->getRules() as $rule) {
            if (true === $rule->byPass()) {
                return;
            }
        }

        /** @var RateLimitService $rateLimitService */
        foreach ($this->rateLimitProviders as $rateLimitService) {
            if (true === $rateLimitService->isRateLimitExceeded($request)) {
                $response = new Response($rateLimitService->getException()->getMessage(), $rateLimitService->getException()->getStatusCode());
                $event->setResponse($response);
            }
        }
    }
}
