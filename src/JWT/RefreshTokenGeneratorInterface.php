<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

interface RefreshTokenGeneratorInterface
{
    public function generate(UserInterface $user): string;
}
