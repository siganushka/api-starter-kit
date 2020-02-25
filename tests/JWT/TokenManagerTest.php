<?php

namespace App\Tests\JWT;

use App\Entity\User;
use App\JWT\Token;
use App\JWT\TokenManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TokenManagerTest extends WebTestCase
{
    public function testTokenManager()
    {
        $user = new User();
        $user->setUsername('siganushka');

        $token = new Token();
        $token->setAccessToken('mock_access_token');
        $token->setRefreshToken('mock_refresh_token');
        $token->setExpiresIn(3600);

        $tokenManager = $this->createMock(TokenManager::class);

        $tokenManager->expects($this->any())
            ->method('create')
            ->willReturn($token);

        $this->assertInstanceOf(\get_class($token), $tokenManager->create($user));
    }
}
