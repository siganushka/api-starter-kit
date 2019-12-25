<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ZoneMatcherListener implements EventSubscriberInterface
{
    const ZONE_ATTRIBUTE = '_zone';

    private $requestMatchers = [];

    public function addRequestMatcher(RequestMatcherInterface $requestMatcher)
    {
        $this->requestMatchers[] = $requestMatcher;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        foreach ($this->requestMatchers as $requestMatcher) {
            if ($requestMatcher->matches($request)) {
                $request->attributes->set(self::ZONE_ATTRIBUTE, true);

                return;
            }
        }

        $request->attributes->set(self::ZONE_ATTRIBUTE, false);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
        ];
    }
}
