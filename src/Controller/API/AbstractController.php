<?php

namespace App\Controller\API;

use App\Exception\APIException;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractController extends AbstractFOSRestController
{
    protected function createAPIException(int $statusCode, string $messageId, array $messageParameters = [], string $domain = null)
    {
        return new APIException($statusCode, $messageId, $messageParameters, $domain);
    }

    protected function createSerializeContext()
    {
        $context = new Context();
        $context->setGroups(['resource', 'sortable', 'enable', 'timestampable']);

        return $context;
    }

    protected function dispatchEvent(object $event, string $eventName = null)
    {
        return $this->get('dispatcher')->dispatch($event, $eventName);
    }

    protected function transMessage(string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        return $this->get('translator')->trans($id, $parameters, $domain, $locale);
    }

    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['translator'] = TranslatorInterface::class;
        $subscribedServices['dispatcher'] = EventDispatcherInterface::class;

        return $subscribedServices;
    }
}
