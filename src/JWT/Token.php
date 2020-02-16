<?php

namespace App\JWT;

class Token
{
    /**
     * 接口调用令牌.
     *
     * @var string
     */
    private $accessToken;

    /**
     * 令牌有效期，单位：秒.
     *
     * @var int
     */
    private $expiresIn;

    /**
     * 令牌类型，默认为 Bearer.
     *
     * @var string
     */
    private $tokenType = 'Bearer';

    /**
     * 刷新令牌，用于刷新 AccessToken，过期后只能重新登录.
     *
     * @var string
     */
    private $refreshToken;

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setTokenType(string $tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function setExpiresIn(int $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
