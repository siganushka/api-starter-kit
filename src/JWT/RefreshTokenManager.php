<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenManager
{
    /**
     * 更新刷新令牌
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function update(UserInterface $user)
    {

    }

    /**
     * 销毁刷新令牌
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function destroy(UserInterface $user)
    {

    }
}
