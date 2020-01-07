<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager
{
    private $jwtEncoder;

    public function __construct(JWTEncoder $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    public function create(UserInterface $user)
    {
        $payload = [
            // 'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
        ];

        return $this->jwtEncoder->encode($payload);
    }

    public function load(string $jwt)
    {
        return $this->jwtEncoder->decode($jwt);
    }
}
