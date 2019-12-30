<?php

namespace App\Controller;

use App\JWT\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @apiSuccess (200) {String} refresh_token 刷新令牌，需要客户端保存，有效期固定 30 天
     * @apiSuccess (200) {String} expires_in 访问令牌有效期，过期后需要客户端主动刷新
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
     * @apiSuccess (200) {String} access_token 新的访问令牌，在客户商替换旧的令牌
     * @apiSuccess (200) {String} refresh_token 新的刷新令牌，在客户商替换旧的刷新令牌
     * @apiSuccess (200) {String} expires_in 新的访问令牌有效期
     */
    public function refreshToken()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/private_area", methods={"GET"})
     *
     * @return void
     */
    public function private_area(JWTManager $jwtManager, UserInterface $user)
    {
        dd($user);
    }

    /**
     * @Route("/public_area", methods={"GET"})
     *
     * @return void
     */
    public function public_area(JWTManager $jwtManager)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->find('App\Entity\User', 1);

        dd($jwtManager->encode($user));

        // dd($jwtManager->decode('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1Nzc3MDEyNjAsImV4cCI6MTU3NzcwNDg2MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoic2lnYW51c2hrYSJ9.F5B_JRYyhNofMYCRqw3AeGF_WLwetS4PLYUBUaPm5wc'));
    }
}
