<?php

namespace App\JWT;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

class JWTProvider
{
    private $signer;
    private $key;

    public function __construct(string $key, int $ttl)
    {
        $this->signer = new Sha256();
        $this->key = new Key($key);
        $this->ttl = $ttl;
    }

    public function create(array $payload, array $header = []): Token
    {
        $builder = new Builder();
        $builder->issuedAt(time());
        $builder->expiresAt(time() + $this->ttl);

        foreach ($header as $key => $value) {
            $builder->withHeader($key, $value);
        }

        foreach ($payload as $key => $value) {
            $builder->withClaim($key, $value);
        }

        try {
            $builder->sign($this->signer, $this->key);
        } catch (\Throwable $th) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        return $builder->getToken();
    }

    public function load(string $jwt): Token
    {
        try {
            $token = (new Parser())->parse($jwt);
        } catch (\Throwable $th) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        $isValid = $token->verify($this->signer, $this->key);
        if (!$isValid) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        $exp = $token->getClaim('exp');
        if ((null === $exp) || !is_numeric($exp)) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        if ((time() - $exp) > 0) {
            throw new \RuntimeException('Expired JWT Token');
        }

        return $token;
    }
}
