<?php

namespace App\JWT;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

class JWSProvider
{
    private $publicKey;
    private $privateKey;
    private $passphrase;
    private $ttl;

    public function __construct(string $publicKey, string $privateKey, string $passphrase, int $ttl)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->passphrase = $passphrase;
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
            $builder->sign(new Sha256(), new Key($this->privateKey, $this->passphrase));
        } catch (\Throwable $th) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        return $builder->getToken();
    }

    public function load(string $jwt): Token
    {
        $token = (new Parser())->parse($jwt);

        $signer = new Sha256();
        $key = new Key($this->privateKey, $this->passphrase);

        $isValid = $token->verify($signer, $key);
        if (!$isValid) {
            throw new \RuntimeException('Invalid JWT Token');
        }

        $exp = $token->getClaim('exp');
        if ((null === $exp) || !is_numeric($exp)) {
            throw new \RuntimeException('Expired JWT Token');
        }

        return $token;
    }
}
