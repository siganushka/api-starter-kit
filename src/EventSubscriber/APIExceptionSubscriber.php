<?php

namespace App\EventSubscriber;

use App\Exception\APIException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class APIExceptionSubscriber implements EventSubscriberInterface
{
    private $viewHandler;

    public function __construct(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    public static function getSubscribedEvents()
    {
        return [ExceptionEvent::class => 'onExceptionEvent'];
    }

    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof APIException) {
            return;
        }

        $view = View::create($exception, $exception->getStatusCode());
        $response = $this->viewHandler->handle($view);

        $event->setResponse($response);
    }
}
