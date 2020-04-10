<?php

namespace App\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

interface RefreshTokenUserInterface extends UserInterface
{
    public function getRefreshToken(): ?string;

    public function setRefreshToken(?string $refreshToken): self;

    public function getRefreshTokenExpiresAt(): ?\DateTimeInterface;

    public function setRefreshTokenExpiresAt(?\DateTimeInterface $refreshTokenExpiresAt): self;

    public function isRefreshTokenExpired(): bool;
}
