<?php

namespace App\Tests\JWT;

use App\Entity\User;
use App\JWT\Token;
use App\JWT\TokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TokenManagerTest extends WebTestCase
{
    public function testTokenManager()
    {
        self::bootKernel();

        self::bootKernel();

        $entityManager = self::$container->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(User::class)
            ->findOneByUsername('siganushka');

        if (!$user) {
            $this->markTestSkipped('must be revisited.');
        }

        $tokenManager = self::$container->get(TokenManager::class);
        $this->assertInstanceOf(Token::class, $tokenManager->create($user));
    }
}
