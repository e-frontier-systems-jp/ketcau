<?php

namespace Ketcau\Command;

use Composer\Console\Input\InputOption;
use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\AbstractEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCsvCommand extends Command
{
    protected $entityManager;

    protected $ketcauConfig;


    public function __construct(EntityManagerInterface $entityManager, KetcauConfig $ketcauConfig)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->ketcauConfig = $ketcauConfig;
    }


    public function getName(): string
    {
        return 'ketcau:import:csv';
    }


    public function configure()
    {
        $this
            ->setDescription('Import CSV file to Database')
            ->addOption('table', null, InputOption::VALUE_REQUIRED, 'Set to the table name.')
            ->addOption('locale', null, InputOption::VALUE_OPTIONAL, 'Locale of import csv file.', 'ja')
        ;
    }


    public function run(InputInterface $input, OutputInterface $output): int
    {
        $table = $input->getOption('table');
        $locale = $input->getOption('locale');

        $projectDir = $this->ketcauConfig->get('kernel.project_dir');
        $path = $projectDir. '/src/Ketcau/Resource/doctrine/import_csv/'. $locale. '/'. $table. '.csv';

        if (!file_exists($path)) {
            $output->writeln(sprintf('<warning>CSV "%s" が見つかりませんでした。</warning>>', $path));
            return Command::FAILURE;
        }

        $file = new \SplFileObject($path);

        $file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::DROP_NEW_LINE
        );

        $lines = [];
        foreach ($file as $line) {
            $lines[] = $line;
        }

        $file = null;

        $connection = $this->entityManager->getConnection();

        $headers = array_shift($lines);

        $count = 0;
        foreach ($lines as $line) {
            $data = [];
            foreach($headers as $key => $header) {
                $data[$header] = $line[$key];
            }

            $connection->insert($table, $data);
            $count++;
        }

        $output->writeln(sprintf('<success>%d 件のデータをインポートしました。</success>', $count));

        return Command::SUCCESS;
    }
}