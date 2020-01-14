<?php

namespace App\JWT;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenManager
{
    private $entityManager;
    private $ttl;

    public function __construct(EntityManagerInterface $entityManager, int $ttl)
    {
        $this->entityManager = $entityManager;
        $this->ttl = $ttl;
    }

    public function loadUserByRefreshToken(string $refreshToken): UserInterface
    {
        $user = $this->entityManager->getRepository('App\Entity\User')
            ->findOneByRefreshToken($refreshToken);

        if (!$user) {
            throw new \RuntimeException('Invalid refresh token');
        }

        return $user;
    }

    public function update(UserInterface $user): string
    {
        $refreshToken = password_hash(uniqid($user->getUsername()), PASSWORD_DEFAULT);
        $refreshToken = str_replace(['+', '/'], ['-', '_'], base64_encode($refreshToken));

        $updatedAt = new \DateTimeImmutable();
        $expiresAt = $updatedAt->modify(sprintf('+%d seconds', $this->ttl));

        $user->setUpdatedAt($updatedAt);
        $user->setRefreshToken($refreshToken);
        $user->setRefreshTokenExpireAt($expiresAt);

        $this->entityManager->flush();

        return $refreshToken;
    }

    public function destroy(UserInterface $user): bool
    {
        $user->setUpdatedAt(new \DateTime());
        $user->setRefreshToken(null);
        $user->setRefreshTokenExpireAt(null);

        $this->entityManager->flush();

        return true;
    }
}
