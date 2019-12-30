<?php

namespace App\JWT;

use App\Entity\UserToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenManager implements RefreshTokenManagerInterface
{
    private $entityManager;
    private $refreshTokenGenerator;

    public function __construct(EntityManagerInterface $entityManager, RefreshTokenGenerator $refreshTokenGenerator, $userProvider)
    {
        $this->entityManager = $entityManager;
        $this->refreshTokenGenerator = $refreshTokenGenerator;
    }

    /**
     * 更新刷新令牌.
     *
     * @return void
     */
    public function update(UserInterface $user): UserToken
    {
        $updatedAt = new \DateTimeImmutable();
        $expireAt = $updatedAt->modify('+15 days');

        $token = $user->getToken();
        $token->setRefreshToken($this->refreshTokenGenerator->generate($user));
        $token->setUpdatedAt($updatedAt);
        $token->setExpireAt($expireAt);

        $this->entityManager->flush();

        return $token;
    }

    /**
     * 销毁刷新令牌.
     *
     * @return void
     */
    public function destroy(UserInterface $user)
    {
    }
}
