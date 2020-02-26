<?php

namespace App\Controller\API;

use App\Response\ErrorResponse;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractController extends AbstractFOSRestController
{
    protected function createErrorResponse(int $status, string $id, array $parameters = [])
    {
        return new ErrorResponse($status, $this->get('translator')->trans($id, $parameters));
    }

    protected function createSerializeContext()
    {
        $context = new Context();
        $context->setSerializeNull(true);
        $context->setGroups(['resource', 'sortable', 'enable', 'timestampable']);

        return $context;
    }

    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['translator'] = TranslatorInterface::class;

        return $subscribedServices;
    }
}
