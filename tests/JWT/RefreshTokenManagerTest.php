<?php

namespace App\Tests\JWT;

use App\JWT\RefreshTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RefreshTokenManagerTest extends WebTestCase
{
    public function testRefreshTokenManager()
    {
        self::bootKernel();

        $userProvider = self::$container->get('security.user.provider.concrete.app_user_provider');
        $refreshTokenManager = self::$container->get(RefreshTokenManagerInterface::class);

        $user = $userProvider->loadUserByUsername('siganushka');
        $refreshToken = $refreshTokenManager->update($user);

        $this->assertIsString($refreshToken);
        $this->assertInstanceOf(UserInterface::class, $refreshTokenManager->loadUserByRefreshToken($refreshToken));

        $ret = $refreshTokenManager->destroy($user);
        $this->assertTrue($ret);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRefreshTokenNotFound()
    {
        self::bootKernel();

        $refreshTokenManager = self::$container->get(RefreshTokenManagerInterface::class);
        $refreshTokenManager->loadUserByRefreshToken('not_exists_refresh_token');
    }
}
