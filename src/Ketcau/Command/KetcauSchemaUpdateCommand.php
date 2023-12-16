<?php

namespace Ketcau\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\SchemaTool;
use Ketcau\Entity\Plugin;
use Ketcau\Repository\PluginRepository;
use Ketcau\Service\PluginService;
use Ketcau\Service\SchemaService;
use Ketcau\Util\StringUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Service\Attribute\Required;

class KetcauSchemaUpdateCommand extends UpdateCommand
{
    protected PluginRepository $pluginRepository;

    protected PluginService $pluginService;

    protected SchemaService $schemaService;


    private ?EntityManagerInterface $entityManager = null;


    public function __construct(?EntityManagerProvider $entityManagerProvider = null)
    {
        parent::__construct($entityManagerProvider);
    }


    #[Required]
    public function setPluginRepository(PluginRepository $pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;
    }

    #[Required]
    public function setPluginService(PluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    #[Required]
    public function setSchemaService(SchemaService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('ketcau:schema:update')
            ->setAliases(['doctrine:schema:update'])
            ->addOption('no-proxy', null, InputOption::VALUE_NONE)
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $noProxy = true === $input->getOption('no-proxy');
        $dumpSql = true === $input->getOption('dump-sql');
        $force = true === $input->getOption('force');

        if ($noProxy || $dumpSql === false && $force === false) {
            return parent::execute($input, $output);
        }

        $tempProxyOutputDir = sys_get_temp_dir(). '/proxy_'. StringUtil::random(12);
        $tempMetaDataOutputDir = sys_get_temp_dir(). '/metadata_'. StringUtil::random(12);

        $generateAllFiles = [];
        try {
            $Plugins = $this->pluginRepository->findAll();
            /** @var Plugin $Plugin */
            foreach ($Plugins as $Plugin) {
                $config = ['code' => $Plugin->getCode()];
                $this->pluginService->generateProxyAndCallback(function ($generateFiles) use (&$generateAllFiles) {
                    $generateAllFiles = array_merge($generateAllFiles, $generateFiles);
                }, $Plugin, $config, false, $tempProxyOutputDir);
            }

            $result = null;
            $command = $this;

            $this->schemaService->executeCallback(function (SchemaTool $schemaTool, array $metaData) use ($command, $input, $output, &$result) {
                $ui = new SymfonyStyle($input, $output);
                if (empty($metaData)) {
                    $ui->success('No Metadata Classes to process.');
                    $result = Command::SUCCESS;
                } else {
                    $result = $command->executeSchemaCommand($input, $output, $schemaTool, $metaData, $ui);
                }

            }, $generateAllFiles, $tempProxyOutputDir, $tempMetaDataOutputDir);

            return $result;
        }
        finally {
            $this->removeOutputDir($tempMetaDataOutputDir);
            $this->removeOutputDir($tempProxyOutputDir);
        }
    }


    protected function removeOutputDir($outputDir)
    {
        if (file_exists($outputDir)) {
            $files = Finder::create()
                ->in($outputDir)
                ->files();

            $f = new Filesystem();
            $f->remove($files);
        }
    }

    public function getHelper(string $name): mixed
    {
        if ('em' === $name) {
            return new EntityManagerHelper($this->entityManager);
        }
        return parent::getHelper($name);
    }
}