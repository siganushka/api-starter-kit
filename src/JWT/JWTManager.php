<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager
{
    private $jwsProvider;

    public function __construct(JWSProvider $jwsProvider)
    {
        $this->jwsProvider = $jwsProvider;
    }

    public function encode(UserInterface $user)
    {
        $payload = [
            'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
        ];

        return $this->jwsProvider->create($payload);
    }

    public function decode(string $jwt)
    {
        return $this->jwsProvider->load($jwt);
    }
}
