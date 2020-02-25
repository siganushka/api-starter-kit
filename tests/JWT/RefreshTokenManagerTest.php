<?php

namespace App\Tests\JWT;

use App\Entity\User;
use App\JWT\RefreshTokenManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RefreshTokenManagerTest extends WebTestCase
{
    public function testRefreshTokenManager()
    {
        $user = new User();
        $user->setUsername('siganushka');

        $refreshTokenManager = $this->createMock(RefreshTokenManager::class);

        $refreshTokenManager->expects($this->any())
            ->method('update')
            ->willReturn('mock_refresh_token');

        $refreshTokenManager->expects($this->any())
            ->method('loadUserByRefreshToken')
            ->willReturn($user);

        $refreshTokenManager->expects($this->any())
            ->method('destroy')
            ->willReturn(true);

        $this->assertIsString($refreshToken = $refreshTokenManager->update($user));
        $this->assertInstanceOf(\get_class($user), $refreshTokenManager->loadUserByRefreshToken($refreshToken));

        $ret = $refreshTokenManager->destroy($user);
        $this->assertTrue($ret);
    }

    public function testRefreshTokenNotFound()
    {
        $this->expectException(\RuntimeException::class);

        $refreshTokenManager = $this->createMock(RefreshTokenManager::class);

        $refreshTokenManager->expects($this->any())
            ->method('loadUserByRefreshToken')
            ->willThrowException(new \RuntimeException('Invalid refresh token'));

        $refreshTokenManager->loadUserByRefreshToken('not_exists_refresh_token');
    }
}
