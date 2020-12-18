<?php

namespace App\Entity;

use App\JWT\RefreshTokenUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Entity\EnableInterface;
use Siganushka\GenericBundle\Entity\EnableTrait;
use Siganushka\GenericBundle\Entity\ResourceInterface;
use Siganushka\GenericBundle\Entity\ResourceTrait;
use Siganushka\GenericBundle\Entity\TimestampableInterface;
use Siganushka\GenericBundle\Entity\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(groups={"username"}, fields={"username"})
 */
class User implements ResourceInterface, EnableInterface, TimestampableInterface, RefreshTokenUserInterface
{
    use ResourceTrait;
    use EnableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @Groups({"user"})
     *
     * @Assert\NotBlank(groups={"username"})
     * @Assert\Regex(groups={"username"}, pattern="/^[a-zA-Z0-9_]+$/")
     * @Assert\Length(groups={"username"}, min=4, max=16)
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(groups={"password"})
     * @Assert\Regex(groups={"password"}, pattern="/^[a-zA-Z0-9_\.\@]+$/")
     * @Assert\Length(groups={"password"}, min=6, max=16)
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     *
     * @Groups({"user_refresh_token"})
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"user_refresh_token"})
     */
    private $refreshTokenExpiresAt;

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getRefreshTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->refreshTokenExpiresAt;
    }

    public function setRefreshTokenExpiresAt(?\DateTimeInterface $refreshTokenExpiresAt): self
    {
        $this->refreshTokenExpiresAt = $refreshTokenExpiresAt;

        return $this;
    }

    public function isRefreshTokenExpired(): bool
    {
        if (null === $this->refreshTokenExpiresAt) {
            return true;
        }

        return (new \DateTime()) > $this->refreshTokenExpiresAt;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
