<?php

namespace App\JWT;

class Token
{
    const TYPE_BEARER = 'Bearer'; // Basic, Digest ...

    /**
     * 用户访问令牌.
     *
     * @var string
     */
    private $accessToken;

    /**
     * 令牌类型，暂时仅支持 Bearer.
     *
     * @var string
     */
    private $tokenType;

    /**
     * 令牌有效期，单位为秒.
     *
     * @var int
     */
    private $expiresIn;

    /**
     * 刷新令牌，用于重新获取访问令牌.
     *
     * @var string
     */
    private $refreshToken;

    public function __construct(string $accessToken, string $tokenType, int $expiresIn, string $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getTokenType()
    {
        return $this->tokenType;
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
