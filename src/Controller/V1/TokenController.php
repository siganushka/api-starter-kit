<?php

namespace App\Controller\V1;

use App\JWT\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function private_area(UserInterface $user)
    {
        dd($user);
    }

    /**
     * @Route("/public_area", name="api_public_area", methods={"GET"})
     *
     * @return void
     */
    public function public_area(JWTManager $JWTManager)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->find('App\Entity\User', 1);

        dd($JWTManager->encode($user));

        // dd($JWTManager->decode('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1Nzc3MDEyNjAsImV4cCI6MTU3NzcwNDg2MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoic2lnYW51c2hrYSJ9.F5B_JRYyhNofMYCRqw3AeGF_WLwetS4PLYUBUaPm5wc'));
    }
}
