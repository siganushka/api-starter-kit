<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager
{
    private $jwtProvider;

    public function __construct(JWTProvider $jwtProvider)
    {
        $this->jwtProvider = $jwtProvider;
    }

    public function encode(UserInterface $user)
    {
        $payload = [
            'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
        ];

        return $this->jwtProvider->create($payload);
    }

    public function decode(string $jwt)
    {
        return $this->jwtProvider->load($jwt);
    }
}
