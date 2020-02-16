<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('siganushka');
        $user->setAvatar('http://placehold.it/320x320');
        $user->setEnabled(true);

        $password = $this->passwordEncoder->encodePassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);
    }
}
