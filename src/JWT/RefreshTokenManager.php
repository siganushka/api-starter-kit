<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenManager
{
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function findUsername(string $refreshToken)
    {
        return $this->redis->get($refreshToken);
    }

    public function update(UserInterface $user): string
    {
        // destory before update...
        $this->destroy($user);

        $refreshToken = $this->generate($user);
        $usernameKey = $this->getUsernameKey($user);

        $this->redis->multi()
            ->set($refreshToken, $user->getUsername())
            ->set($usernameKey, $refreshToken)
            ->exec();

        return $refreshToken;
    }

    public function destroy(UserInterface $user)
    {
        $usernameKey = $this->getUsernameKey($user);

        $deletedKeys = [$usernameKey];
        if (false !== $refreshToken = $this->redis->get($usernameKey)) {
            array_push($deletedKeys, $refreshToken);
        }

        $this->redis->del($deletedKeys);
    }

    public function generate(UserInterface $user): string
    {
        $hash = password_hash(uniqid($user->getUsername()), PASSWORD_DEFAULT);
        $hash = str_replace(['+', '/'], ['-', '_'], base64_encode($hash));

        return $hash;
    }

    public function getUsernameKey(UserInterface $user): string
    {
        return sprintf('user_%s_refresh_token', $user->getUsername());
    }
}
