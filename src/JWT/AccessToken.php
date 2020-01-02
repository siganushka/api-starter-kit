<?php

namespace App\JWT;

class AccessToken
{
    private $accessToken;
    private $refreshToken;
    private $expiresIn;

    public function __construct(string $accessToken, string $refreshToken, int $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getTokenType()
    {
        return 'Bearer';
    }

    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
}
