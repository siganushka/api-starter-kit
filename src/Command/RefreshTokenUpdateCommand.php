<?php

namespace App\Command;

use App\JWT\RefreshTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RefreshTokenUpdateCommand extends Command
{
    protected static $defaultName = 'app:refresh-token:update';

    private $entityManager;
    private $refreshTokenManager;

    public function __construct(EntityManagerInterface $entityManager, RefreshTokenManager $refreshTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->refreshTokenManager = $refreshTokenManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('更新用户的刷新令牌')
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
            $this->refreshTokenManager->update($user);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());

            return 1;
        }

        $io->success(sprintf('用户 "%s" 刷新令牌已更新成功！', $username));

        return 0;
    }
}
