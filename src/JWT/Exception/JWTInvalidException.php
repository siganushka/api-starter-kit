<?php

namespace App\JWT\Exception;

class JWTInvalidException extends \InvalidArgumentException
{
    private $tokenAsString;

    public function __construct(string $tokenAsString)
    {
        $this->tokenAsString = $tokenAsString;

        parent::__construct('Invalid token.');
    }

    public function getTokenAsString()
    {
        return $this->tokenAsString;
    }
}
