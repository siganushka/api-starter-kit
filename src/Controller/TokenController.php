<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @apiDefine TokenModel
 *
 * @apiSuccess (200) {String} access_token 访问令牌，需要客户端保存
 * @apiSuccess (200) {String} refresh_token 刷新令牌，需要客户端保存，有效期固定 30 天
 * @apiSuccess (200) {String} expires_in 访问令牌有效期，过期后需要客户端主动刷新
 */
class TokenController extends AbstractController
{
    /**
     * @Route("/token", name="api_token", methods={"POST"})
     *
     * @api {post} /token 获取认证令牌
     *
     * @apiGroup Token
     * @apiVersion 0.1.0
     *
     * @apiParam (Body) {String} username 用户登录名
     * @apiParam (Body) {String} password 用户登录密码
     *
     * @apiUse TokenModel
     */
    public function token()
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
     * @apiHeader {String} Authorization 刷新令牌，使用 <code>Bearer :refresh_token</code> 格式
     *
     * @apiUse TokenModel
     */
    public function refreshToken()
    {
        // controller can be blank: it will never be executed!
    }
}
