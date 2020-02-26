<?php

namespace App\Controller\API;

use App\Response\ErrorResponse;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @apiDefine Error
 *
 * @apiError (Error 4xx、5xx) {String} type 错误响应规范地址
 * @apiError (Error 4xx、5xx) {String} title 错误标题
 * @apiError (Error 4xx、5xx) {String} status 错误的状态码 <code>HTTP Status Code</code>
 * @apiError (Error 4xx、5xx) {String} detail 错误消息，可直接在客户单显示给用户
 * @apiError (Error 4xx、5xx) {Object} invalid_params 无效的参数列表，仅在状态码为 <code>422</code> 时出现该字段
 */
abstract class AbstractController extends AbstractFOSRestController
{
    protected function createErrorResponse(int $status, string $id, array $parameters = [])
    {
        return new ErrorResponse($status, $this->trans($id, $parameters));
    }

    protected function createSerializeContext()
    {
        $context = new Context();
        $context->setSerializeNull(true);
        $context->setGroups(['resource', 'sortable', 'enable', 'timestampable']);

        return $context;
    }

    protected function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        return $this->get('translator')->trans($id, $parameters, $domain, $locale);
    }

    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['translator'] = TranslatorInterface::class;

        return $subscribedServices;
    }
}
