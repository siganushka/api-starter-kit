<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

interface RefreshTokenManagerInterface
{
    /**
     * 根据刷新令牌加载用户.
     *
     * @param string $refreshToken 刷新令牌
     *
     * @throws \RuntimeException
     */
    public function loadUserByRefreshToken(string $refreshToken): UserInterface;

    /**
     * 更新用户刷新令牌.
     */
    public function update(UserInterface $user): string;

    /**
     * 销毁用户刷新令牌.
     */
    public function destroy(UserInterface $user): bool;

    /**
     * 生成刷新令牌.
     */
    public function generate(UserInterface $user): string;
}
