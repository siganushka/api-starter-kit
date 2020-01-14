<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class AccessTokenManager
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

    public function create(UserInterface $user): AccessToken
    {
        $payload = [
            // 'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
        ];

        $refreshToken = $this->refreshTokenManager->update($user);
        $jwt = $this->jwtManager->create($payload);

        $accessToken = new AccessToken();
        $accessToken->setAccessToken($jwt);
        $accessToken->setRefreshToken($refreshToken);
        $accessToken->setExpiresIn($this->ttl);

        return $accessToken;
    }
}