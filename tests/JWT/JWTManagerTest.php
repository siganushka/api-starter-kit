<?php

namespace App\Tests\JWT;

use App\JWT\JWTManager;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;

class JWTManagerTest extends TestCase
{
    public function testAll()
    {
        $jwtManager = new JWTManager('test', 3600);

        $payload = [
            'email' => 'foo@bar.com',
        ];

        $headers = [
            'ext' => 'info...',
        ];

        $jwt = $jwtManager->create($payload, $headers);
        $this->assertInstanceOf(Token::class, $jwt);
        $this->assertEquals(2, substr_count($jwt, '.'));

        $token = $jwtManager->parse($jwt);
        $this->assertEquals($payload['email'], $token->getClaim('email'));
        $this->assertEquals($headers['ext'], $token->getHeader('ext'));
    }
}
