<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="api_user_get", methods={"GET"})
     */
    public function _get()
    {
        $user = $this->getUser();

        $context = $this->createSerializeContext();
        $context->addGroup('user');

        $view = $this->view($user);
        $view->setContext($context);

        return $view;
    }
}
