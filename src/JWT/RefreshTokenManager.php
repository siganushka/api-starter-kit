<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RefreshTokenManager implements RefreshTokenManagerInterface
{
    private $provider;
    private $redis;

    public function __construct(UserProviderInterface $provider, \Redis $redis)
    {
        $this->provider = $provider;
        $this->redis = $redis;
    }

    public function loadUserByRefreshToken(string $refreshToken): UserInterface
    {
        $username = $this->redis->get($refreshToken);
        if (!$username) {
            throw new \RuntimeException("Invalid refresh token: {$refreshToken}");
        }

        return $this->provider->loadUserByUsername($username);
    }

    public function update(UserInterface $user): string
    {
        // destory before update...
        $username = $user->getUsername();
        if (false !== $dirtyValue = $this->redis->get($username)) {
            $this->redis->del($dirtyValue);
        }

        $refreshToken = $this->generate($user);
        $this->redis->multi()
            ->set($refreshToken, $username)
            ->set($username, $refreshToken)
            ->exec();

        return $refreshToken;
    }

    public function destroy(UserInterface $user): bool
    {
        $username = $user->getUsername();

        $deletedKeys = [$username];
        if (false !== $dirtyValue = $this->redis->get($username)) {
            array_push($deletedKeys, $dirtyValue);
        }

        $this->redis->del($deletedKeys);

        return true;
    }

    public function generate(UserInterface $user): string
    {
        $hash = password_hash(uniqid($user->getUsername()), PASSWORD_DEFAULT);
        $hash = str_replace(['+', '/'], ['-', '_'], base64_encode($hash));

        return $hash;
    }
}
