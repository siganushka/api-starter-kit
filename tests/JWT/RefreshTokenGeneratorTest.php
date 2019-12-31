<?php

namespace App\Tests\JWT;

use App\Entity\User;
use App\JWT\RefreshTokenGenerator;
use PHPUnit\Framework\TestCase;

class RefreshTokenGeneratorTest extends TestCase
{
    public function testGenerateRefreshToken()
    {
        $user = new User();
        $user->setUsername('siganushka');
        $user->setAvatar('http://placehold.it/320x320');
        $user->setCreatedAt(new \DateTimeImmutable());

        $generator = new RefreshTokenGenerator();

        $this->assertNotEmpty($generator->generate($user));
    }
}
