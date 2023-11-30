<?php

namespace Ketcau\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'ketcau:install',
    description: 'Install Ketcau',
    hidden: false
)]
class InstallerCommand extends Command
{
    protected $container;

    protected $io;

    protected $databaseUrl;


    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }


    protected function configure()
    {
        $this->setDescription('Install Ketcau');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Ketcau Installer Interactive Wizard');
        $this->io->text([

        ]);

        $databaseUrl = $this->container->getParameter('ketcau_database_url');
        if (empty($databaseUrl)) {
            $databaseUrl = 'sqlite:///var/ketcau.db';
        }
        $databaseUrl = $this->io->ask('Database Url', $databaseUrl);

    }
}