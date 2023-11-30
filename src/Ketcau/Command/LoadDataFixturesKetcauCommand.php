<?php

namespace Ketcau\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDataFixturesKetcauCommand extends DoctrineCommand
{
    protected static $defaultName = 'ketcau:fixtures:load';


    protected $containers;


    public function __construct(ManagerRegistry $registry, ContainerInterface $containers)
    {
        parent::__construct($registry);
        $this->containers = $containers;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager(null);

        //$locale = env('KETCAU_LOCALE', 'ja_JP');
        $locale = 'ja_JP';
        $locale = str_replace('_', '-', $locale);
        $locales = \Locale::parseLocale($locale);
        $localeDir = is_null($locales) ? 'ja' : $locales['language'];

        $loader = new \Ketcau\Doctrine\Common\CsvDataFixtures\Loader();
        $files = $loader->loadFromDirectory(__DIR__. '/../Resource/doctrine/import_csv/'. $localeDir);

        foreach($files as $file) {
            $file->load($em);
        }
    }
}