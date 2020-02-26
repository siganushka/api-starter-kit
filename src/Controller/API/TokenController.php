<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @apiDefine TokenModel
 *
 * @apiSuccess (Success 2xx) {String} access_token 访问令牌，需要客户端保存
 * @apiSuccess (Success 2xx) {String} token_type 令牌类型，暂时仅支持 <code>Bearer</code>
 * @apiSuccess (Success 2xx) {Number} expires_in 访问令牌有效期，过期后需要客户端主动刷新，单位：秒
 * @apiSuccess (Success 2xx) {String} refresh_token 刷新令牌，用于换取新的访问令牌，需要客户端保存，有效期暂定 <code>1</code> 个月
 */
class TokenController extends AbstractController
{
    /**
     * @Route("/token", name="api_token", methods={"POST"})
     *
     * @api {post} /token 1、获取认证令牌
     *
     * @apiGroup 帐户
     * @apiVersion 0.1.0
     *
     * @apiParam (Body) {String} username 用户登录名
     * @apiParam (Body) {String} password 用户登录密码
     *
     * @apiUse TokenModel
     * @apiUse Error
     */
    public function token()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/refresh_token", name="api_refresh_token", methods={"POST"})
     *
     * @api {post} /refresh_token 2、刷新认证令牌
     *
     * @apiGroup 帐户
     * @apiVersion 0.1.0
     *
     * @apiParam (Body) {String} refresh_token 刷新令牌
     *
     * @apiUse TokenModel
     * @apiUse Error
     */
    public function refreshToken()
    {
        // controller can be blank: it will never be executed!
    }
}
