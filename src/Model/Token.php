<?php

namespace App\Model;

class Token
{
    /**
     * 用户访问令牌
     *
     * @var string
     */
    private $accessToken;

    /**
     * 刷新令牌，用于重新获取访问令牌
     *
     * @var string
     */
    private $refreshToken;

    /**
     * 令牌有效期，单位为秒
     *
     * @var int
     */
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

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function getExpiresIn()
    {
        return $this->expiresIn;
    }
}
