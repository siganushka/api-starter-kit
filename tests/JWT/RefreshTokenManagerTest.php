<?php

namespace App\Tests\JWT;

use App\JWT\RefreshTokenManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\User;

class RefreshTokenManagerTest extends TestCase
{
    public function testRefreshTokenManager()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);

        $refreshTokenManager = new RefreshTokenManager($redis);
        $this->assertFalse($refreshTokenManager->findUsername('foo'));

        $user = new User('siganushka', '123456', ['USER_ROLE']);
        $this->assertIsString($refreshToken = $refreshTokenManager->update($user));
        $this->assertEquals('siganushka', $refreshTokenManager->findUsername($refreshToken));

        $refreshTokenManager->destroy($user);
        $this->assertEmpty($refreshTokenManager->findUsername($refreshToken));
    }
}
