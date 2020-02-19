<?php

namespace App\JWT;

use Doctrine\ORM\EntityManagerInterface;

class RefreshTokenManager
{
    private $entityManager;
    private $ttl;

    public function __construct(EntityManagerInterface $entityManager, int $ttl)
    {
        $this->entityManager = $entityManager;
        $this->ttl = $ttl;
    }

    public function loadUserByRefreshToken(string $refreshToken): RefreshTokenUserInterface
    {
        $user = $this->entityManager->getRepository('App\Entity\User')
            ->findOneByRefreshToken($refreshToken);

        if (!$user) {
            throw new \RuntimeException('Invalid refresh token');
        }

        return $user;
    }

    public function update(RefreshTokenUserInterface $user): string
    {
        $refreshToken = password_hash(uniqid($user->getUsername()), PASSWORD_DEFAULT);
        $refreshToken = str_replace(['+', '/'], ['-', '_'], base64_encode($refreshToken));

        $datetime = new \DateTime();
        $expireAt = $datetime->modify(sprintf('+%d seconds', $this->ttl));

        $user->setRefreshToken($refreshToken);
        $user->setRefreshTokenExpireAt($expireAt);

        $this->entityManager->flush();

        return $refreshToken;
    }

    public function destroy(RefreshTokenUserInterface $user): bool
    {
        $user->setRefreshToken(null);
        $user->setRefreshTokenExpireAt(null);

        $this->entityManager->flush();

        return true;
    }
}
