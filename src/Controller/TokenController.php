<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @apiDefine TokenModel
 *
 * @apiSuccess (Success 200) {String} access_token 访问令牌，需要客户端保存
 * @apiSuccess (Success 200) {String} token_type 令牌类型，暂时仅支持 <code>Bearer</code>
 * @apiSuccess (Success 200) {String} expires_in 访问令牌有效期，过期后需要客户端主动刷新
 * @apiSuccess (Success 200) {String} refresh_token 刷新令牌，需要客户端保存，有效期固定 30 天
 */
class TokenController extends AbstractFOSRestController
{
    /**
     * @Route("/access_token", name="api_access_token", methods={"POST"})
     *
     * @api {post} /access_token 获取认证令牌
     *
     * @apiGroup Token
     * @apiVersion 0.1.0
     *
     * @apiParam (Body) {String} username 用户登录名
     * @apiParam (Body) {String} password 用户登录密码
     *
     * @apiUse TokenModel
     */
    public function accessToken()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/refresh_token", name="api_refresh_token", methods={"POST"})
     *
     * @api {post} /refresh_token 刷新认证令牌
     *
     * @apiGroup Token
     * @apiVersion 0.1.0
     *
     * @apiHeader {String} Authorization 刷新令牌，使用 <code>Bearer {refresh_token}</code> 格式
     *
     * @apiUse TokenModel
     */
    public function refreshToken()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/private_area", name="api_private_area", methods={"GET"})
     *
     * @return void
     */
    public function privateArea(UserInterface $user)
    {
        $view = $this->view($user);

        $context = $view->getContext();
        $context->setSerializeNull(true);
        $context->addGroup('user');

        $view->setContext($context);

        return $view;
    }

    /**
     * @Route("/public_area", name="api_public_area", methods={"GET"})
     *
     * @return void
     */
    public function publicArea()
    {
        return $this->view(['success' => 'Hello World']);
    }
}
