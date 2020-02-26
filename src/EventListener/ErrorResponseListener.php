<?php

namespace App\EventListener;

use App\Response\ErrorResponse;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ErrorResponseListener implements EventSubscriberInterface
{
    private $viewHandler;

    public function __construct(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => 'onKernelView'];
    }

    public function onKernelView(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        if (!$result instanceof ErrorResponse) {
            return;
        }

        $view = View::create($result, $result->getStatus());
        $response = $this->viewHandler->handle($view);

        $event->setResponse($response);
    }
}
