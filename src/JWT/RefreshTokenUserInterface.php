<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

interface RefreshTokenUserInterface extends UserInterface
{
    public function getRefreshToken(): ?string;

    public function setRefreshToken(?string $refreshToken): self;

    public function getRefreshTokenExpireAt(): ?\DateTimeInterface;

    public function setRefreshTokenExpireAt(?\DateTimeInterface $refreshTokenExpireAt): self;

    public function isRefreshTokenExpired(): bool;
}
