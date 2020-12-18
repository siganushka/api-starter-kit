<?php

namespace App\JWT;

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

    public function create(RefreshTokenUserInterface $user): Token
    {
        $payload = [
            'username' => $user->getUsername(),
        ];

        $refreshToken = $this->refreshTokenManager->update($user);
        $jwt = $this->jwtManager->create($payload);

        $token = new Token();
        $token->setAccessToken($jwt->toString());
        $token->setRefreshToken($refreshToken);
        $token->setExpiresIn($this->ttl);

        return $token;
    }
}
