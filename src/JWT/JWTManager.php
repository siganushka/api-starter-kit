<?php

namespace App\JWT;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;

class JWTManager
{
    private $signer;
    private $secret;
    private $ttl;

    public function __construct(string $secret, int $ttl)
    {
        $this->signer = new Sha256();
        $this->secret = $secret;
        $this->ttl = $ttl;
    }

    public function create(array $payload, array $header = []): Token
    {
        $expiresAt = new \DateTime();
        $expiresAt->modify(sprintf('+%d seconds', $this->ttl));

        $builder = new Builder();
        $builder->issuedAt(time());
        $builder->expiresAt($expiresAt->getTimestamp());

        foreach ($payload as $key => $value) {
            $builder->withClaim($key, $value);
        }

        foreach ($header as $key => $value) {
            $builder->withHeader($key, $value);
        }

        try {
            $builder->sign($this->signer, $this->secret);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $builder->getToken();
    }

    public function parse(string $jwt): Token
    {
        $token = (new Parser())->parse($jwt);

        $isValid = $token->verify($this->signer, $this->secret);
        if (!$isValid) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        if ($token->isExpired()) {
            throw new \RuntimeException('Expired JWT Token');
        }

        return $token;
    }
}
