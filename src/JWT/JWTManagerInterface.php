<?php

namespace App\JWT;

use Lcobucci\JWT\Token;

interface JWTManagerInterface
{
    /**
     * Create JSON web token.
     *
     * @param array $payload Payload for JSON web token
     * @param array $header  Headers for JSON web token
     */
    public function create(array $payload, array $header = []): Token;

    /**
     * Parse JSON web token.
     *
     * @param string $jwt JSON web token
     */
    public function parse(string $jwt): Token;
}
