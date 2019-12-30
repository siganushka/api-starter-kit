<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenGenerator implements RefreshTokenGeneratorInterface
{
    public function generate(UserInterface $user): string
    {
        $token = password_hash($user->getUsername(), PASSWORD_DEFAULT);
        $token = str_replace(['+', '/'], ['-', '_'], base64_encode($token));

        return $token;
    }
}
