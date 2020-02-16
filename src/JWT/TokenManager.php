<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class TokenManager
{
    private $jwtManager;
    private $refreshTokenManager;
    private $ttl;

    public function __construct(JWTManager $jwtManager, RefreshTokenManager $refreshTokenManager, int $ttl)
    {
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->ttl = $ttl;
    }

    public function create(UserInterface $user): Token
    {
        $payload = [
            // 'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
        ];

        $refreshToken = $this->refreshTokenManager->update($user);
        $jwt = $this->jwtManager->create($payload);

        $token = new Token();
        $token->setAccessToken($jwt);
        $token->setRefreshToken($refreshToken);
        $token->setExpiresIn($this->ttl);

        return $token;
    }
}
