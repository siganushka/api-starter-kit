<?php

namespace App\Tests\JWT;

use App\JWT\JWTEncoder;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;

class JWTEncoderTest extends TestCase
{
    public function testEncoder()
    {
        $jwtEncoder = new JWTEncoder('foo', 3600);

        $payload = [
            'uid' => 1024,
            'email' => 'foo@bar.com',
        ];

        $headers = [
            'ext' => 'info...',
        ];

        $jwt = $jwtEncoder->encode($payload, $headers);
        $this->assertInstanceOf(Token::class, $jwt);
        $this->assertIsString((string) $jwt);

        $token = $jwtEncoder->decode($jwt);

        $this->assertEquals($payload['uid'], $token->getClaim('uid'));
        $this->assertEquals($payload['email'], $token->getClaim('email'));
        $this->assertEquals($headers['ext'], $token->getHeader('ext'));
    }
}
