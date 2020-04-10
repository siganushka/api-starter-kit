<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    /**
     * @Route("/token", name="api_token_post", methods={"POST"})
     */
    public function _post()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/token", name="api_token_put", methods={"PUT"})
     */
    public function _put()
    {
        // controller can be blank: it will never be executed!
    }
}
