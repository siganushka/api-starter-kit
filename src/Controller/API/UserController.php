<?php

namespace App\Controller\API;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @apiDefine UserModel
 *
 * @apiSuccess (Success 2xx) {String} id 用户 ID
 * @apiSuccess (Success 2xx) {String} username 用户名（唯一标识）
 * @apiSuccess (Success 2xx) {String} avatar 用户头像
 * @apiSuccess (Success 2xx) {String} updated_at 最后更新时间
 * @apiSuccess (Success 2xx) {String} created_at 用户创建时间
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users/current", name="api_users_current", methods={"GET"})
     *
     * @api {get} /users/current 3、获取当前用户信息
     *
     * @apiGroup 帐户
     * @apiVersion 0.1.0
     *
     * @apiUse UserModel
     * @apiUse Error
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
