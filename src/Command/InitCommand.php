<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitCommand extends Command
{
    protected static $defaultName = 'app:init';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('初始化项目，用于初次部署')
            ->addOption('primary_key_start', null, InputOption::VALUE_REQUIRED, '主键起始值', 65536)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $primaryKeyStart = (int) $input->getOption('primary_key_start');

        if ($primaryKeyStart <= 0) {
            $io->error('primary_key_start: 主键起始值无效');

            return 1;
        }

        $entities = $this->entityManager->getMetadataFactory();
        foreach ($entities->getAllMetadata() as $metadata) {
            $query = $this->entityManager->getRepository($metadata->getName())
                ->createQueryBuilder('u')
                ->select('COUNT(u)')
                ->getQuery();

            $numRows = (int) $query->getSingleScalarResult();
            if ($numRows > 0) {
                $io->error(sprintf('表 %s 已经包含 %d 条数据，不能被初始化', $metadata->getTableName(), $numRows));

                return 1;
            }
        }

        $connection = $this->entityManager->getConnection();
        foreach ($entities->getAllMetadata() as $metadata) {
            $stmt = $connection->prepare(sprintf('ALTER TABLE `%s` AUTO_INCREMENT=%d', $metadata->getTableName(), $primaryKeyStart));
            $stmt->execute();
        }

        $io->success('项目已初始化完成！');

        return 0;
    }
}
