<?php

namespace App\JWT;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\MicrosecondBasedDateConversion;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;

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
        $issuedAt = new \DateTimeImmutable();
        $expiresAt = $issuedAt->modify(sprintf('+%d seconds', $this->ttl));

        $jws = new Builder(new JoseEncoder(), new MicrosecondBasedDateConversion());
        $jws->issuedAt($issuedAt);
        $jws->expiresAt($expiresAt);

        foreach ($payload as $key => $value) {
            $jws->withClaim($key, $value);
        }

        foreach ($header as $key => $value) {
            $jws->withHeader($key, $value);
        }

        return $jws->getToken($this->signer, InMemory::plainText($this->secret));
    }

    public function parse(string $jwt): Token
    {
        if (!preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/', $jwt)) {
            throw new Exception\JWTInvalidException($jwt);
        }

        $jws = (new Parser(new JoseEncoder()))->parse((string) $jwt);

        $isValid = (new Validator())->validate($jws, new SignedWith($this->signer, InMemory::plainText($this->secret)));
        if (!$isValid) {
            throw new Exception\JWTInvalidException($jwt);
        }

        $now = new \DateTime();
        if ($jws->isExpired($now)) {
            throw new Exception\JWTExpiredException($jwt);
        }

        return $jws;
    }
}
