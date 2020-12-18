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
        $this->assertEquals(2, substr_count($jwt->toString(), '.'));

        $jws = $jwtManager->parse($jwt->toString());

        $this->assertEquals($headers['ext'], $jws->headers()->get('ext'));
        $this->assertEquals($payload['email'], $jws->claims()->get('email'));
    }
}
