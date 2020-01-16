<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(groups={"username"}, fields={"username"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Groups({"user"})
     */
    private $id;

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
     * @ORM\Column(type="string")
     *
     * @Groups({"user"})
     */
    private $avatar;

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
    private $refreshTokenExpireAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"user"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @Groups({"user"})
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

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

    public function getRefreshTokenExpireAt(): ?\DateTimeInterface
    {
        return $this->refreshTokenExpireAt;
    }

    public function setRefreshTokenExpireAt(?\DateTimeInterface $refreshTokenExpireAt): self
    {
        $this->refreshTokenExpireAt = $refreshTokenExpireAt;

        return $this;
    }

    public function isRefreshTokenExpired()
    {
        if (null === $this->refreshTokenExpireAt) {
            return true;
        }

        return (new \DateTime()) > $this->refreshTokenExpireAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
