<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserDeleteCommand extends Command
{
    protected static $defaultName = 'app:user:delete';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('删除用户')
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

        if (!$io->confirm(sprintf('删除用户 “%s” 和其相关的数据，此操作不可恢复，确定要继续吗？', $username), false)) {
            return 0;
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $io->success(sprintf('用户 "%s" 已删除成功！', $username));

        return 0;
    }
}
