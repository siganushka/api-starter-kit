<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    private $validator;
    private $entityManager;
    private $passwordEncoder;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('添加用户')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, '用户名，必须唯一')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, '用户登录密码，长度 6-16 位')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getOption('username') ?: '';
        $password = $input->getOption('password') ?: '';

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setAvatar('http://placehold.it/320x320');
        $user->setEnabled(true);

        $violations = $this->validator->validate($user, null, ['username', 'password']);
        if (\count($violations) > 0) {
            $io->error(sprintf('%s: %s', $violations[0]->getPropertyPath(), $violations[0]->getMessage()));

            return 1;
        }

        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('用户 "%s" 已添加成功！', $username));

        return 0;
    }
}
