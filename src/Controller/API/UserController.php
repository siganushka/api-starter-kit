<?php

namespace App\Controller\API;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @apiDefine UserModel
 *
 * @apiSuccess (Success 200) {String} id 用户 ID
 * @apiSuccess (Success 200) {String} username 用户名（唯一标识）
 * @apiSuccess (Success 200) {String} avatar 用户头像
 * @apiSuccess (Success 200) {String} updated_at 最后更新时间
 * @apiSuccess (Success 200) {String} created_at 用户创建时间
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users/current", name="api_users_current", methods={"GET"})
     *
     * @api {get} /users/current 1、获取当前用户信息
     *
     * @apiGroup User
     * @apiVersion 0.1.0
     *
     * @apiUse UserModel
     *
     * @return Response
     */
    public function current(UserInterface $user)
    {
        $context = $this->createSerializeContext();
        $context->addGroup('user');

        $view = $this->view($user);
        $view->setContext($context);

        return $view;
    }
}
