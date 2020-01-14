<?php

namespace App\Tests\JWT;

use App\Entity\User;
use App\JWT\RefreshTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RefreshTokenManagerTest extends WebTestCase
{
    public function testRefreshTokenManager()
    {
        self::bootKernel();

        $entityManager = self::$container->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(User::class)
            ->findOneByUsername('siganushka');

        if (!$user) {
            $this->markTestSkipped('must be revisited.');
        }

        $refreshTokenManager = self::$container->get(RefreshTokenManager::class);
        $this->assertIsString($refreshToken = $refreshTokenManager->update($user));
        $this->assertInstanceOf(User::class, $refreshTokenManager->loadUserByRefreshToken($refreshToken));

        $ret = $refreshTokenManager->destroy($user);
        $this->assertTrue($ret);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRefreshTokenNotFound()
    {
        self::bootKernel();

        $refreshTokenManager = self::$container->get(RefreshTokenManager::class);
        $refreshTokenManager->loadUserByRefreshToken('not_exists_refresh_token');
    }
}
