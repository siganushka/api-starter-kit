<?php

namespace App\Command;

use App\JWT\RefreshTokenManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RefreshTokenDestroyCommand extends Command
{
    protected static $defaultName = 'app:refresh-token:destroy';

    private $entityManager;
    private $refreshTokenManager;

    public function __construct(EntityManagerInterface $entityManager, RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->refreshTokenManager = $refreshTokenManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('销毁指定用户的刷新令牌，该功能用于令牌被盗、用户重置密码等功能')
            ->addArgument('username', InputArgument::REQUIRED, '用户名（唯一标识）')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $user = $this->entityManager->getRepository('App\Entity\User')
            ->findOneByUsername($username);

        if (null === $user) {
            $io->error(sprintf('用户名 "%s" 未找到', $username));

            return 1;
        }

        try {
            $this->refreshTokenManager->destroy($user);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return 1;
        }

        $io->success(sprintf('用户 "%s" 令牌已销毁成功', $username));

        return 0;
    }
}
