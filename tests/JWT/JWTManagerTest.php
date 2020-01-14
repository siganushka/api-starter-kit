<?php

namespace App\Tests\JWT;

use App\JWT\JWTManagerInterface;
use Lcobucci\JWT\Token;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JWTManagerTest extends WebTestCase
{
    public function testJWTManager()
    {
        self::bootKernel();

        $jwtManager = self::$container->get(JWTManagerInterface::class);

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
