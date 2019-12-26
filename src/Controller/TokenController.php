<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @apiSuccess (200) {String} access_token 访问令牌，需要客户端保存
     * @apiSuccess (200) {String} expires_in 令牌有效期，过期后需要客户端主动刷新
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
     * @apiParam (Body) {String} username 用户登录名
     * @apiParam (Body) {String} password 用户登录密码
     *
     * @apiSuccess (200) {String} access_token 访问令牌，需要客户端保存
     * @apiSuccess (200) {String} expires_in 令牌有效期，过期后需要客户端主动刷新
     */
    public function refreshToken()
    {
        // controller can be blank: it will never be executed!
    }
}
