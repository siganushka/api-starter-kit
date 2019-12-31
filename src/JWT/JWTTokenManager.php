<?php

namespace App\JWT;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTTokenManager
{
    private $publicKey;
    private $privateKey;
    private $passphrase;
    private $ttl;

    public function __construct(string $publicKey, string $privateKey, string $passphrase, int $ttl = 3600)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->passphrase = $passphrase;
        $this->ttl = $ttl;
    }

    public function encode(UserInterface $user): Token
    {
        $jws = new Builder();
        $jws->setIssuedAt(time());
        $jws->setExpiration(time() + $this->ttl);
        // $jws->set('roles', $user->getRoles());
        $jws->set('username', $user->getUsername());

        $signer = new Sha256();
        $key = new Key($this->privateKey, $this->passphrase);

        try {
            $jws->sign($signer, $key);
        } catch (\Throwable $th) {
            // Invalid JWT Token
        }

        return $jws->getToken();
    }

    public function decode(string $token): Token
    {
        $jws = (new Parser())->parse($token);

        $signer = new Sha256();
        $key = new Key($this->privateKey, $this->passphrase);

        $isValid = $jws->verify($signer, $key);
        if (!$isValid) {
            // Invalid JWT Token
        }

        $exp = $jws->getClaim('exp');
        if ((null === $exp) || !is_numeric($exp)) {
            // Expired JWT Token
        }

        return $jws;
    }
}
