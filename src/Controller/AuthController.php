<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth/user", name="api_auth_user", methods={"GET"})
     */
    public function user()
    {
        return $this->json($this->getUser());
    }

    /**
     * @Route("/auth/token", name="api_auth_token", methods={"POST"})
     */
    public function token()
    {
        // nothing todo...
    }
}
