<?php

namespace Ketcau\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Ketcau\Doctrine\ORM\Mapping\Driver\NopAnnotationDriver;
use Ketcau\Doctrine\ORM\Mapping\Driver\ReloadSafeAnnotationDriver;
use Ketcau\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class SchemaService
{
    protected $entityManager;

    private $pluginContext;


    public function __construct(EntityManagerInterface $entityManager, PluginContext $pluginContext)
    {
        $this->entityManager = $entityManager;
        $this->pluginContext = $pluginContext;
    }


    public function executeCallback(Callable $callback, $generatedFiles, $proxiesDirectory, $outputDir = null)
    {
        $createOutputDir = false;
        if (is_null($outputDir)) {
            $outputDir = sys_get_temp_dir(). '/metadata_'. StringUtil::random(12);
            mkdir($outputDir);
            $createOutputDir = true;
        }

        try {
            $chain = $this->entityManager->getConfiguration()->getMetadataDriverImpl()->getDriver();
            $drivers = $chain->getDrivers();
            foreach ($drivers as $namespace => $oldDriver) {
                if ('Ketcau\Entity' === $namespace || preg_match('/^Plugin\\\\.*\\\\Entity$/', $namespace)) {
                    $newDriver = new ReloadSafeAnnotationDriver(
                        new AnnotationReader(),
                        $oldDriver->getPaths()
                    );
                    $newDriver->setFileExtension($oldDriver->getFileExtension());
                    $newDriver->addExcludePaths($oldDriver->getExcludePaths());
                    $newDriver->setTraitProxiesDirectory($proxiesDirectory);
                    $newDriver->setNewProxyFiles($generatedFiles);
                    $newDriver->setOutputDir($outputDir);
                    $chain->addDriver($newDriver, $namespace);
                }

                if ($this->pluginContext->isUninstall()) {
                    foreach ($this->pluginContext->getExtraEntityNamespace() as $extraEntityNamespace) {
                        if ($extraEntityNamespace === $namespace) {
                            $chain->addDriver(new NopAnnotationDriver(new AnnotationReader()), $namespace);
                        }
                    }
                }
            }

            $tool = new SchemaTool($this->entityManager);
            $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

            call_user_func($callback, $tool, $metaData);
        }
        finally {
            if ($createOutputDir) {
                $files = Finder::create()
                    ->in($outputDir)
                    ->files();
                $f = new Filesystem();
                $f->remove($files);
            }
        }
    }


    public function updateSchema($generatedFiles, $proxiesDirectory, $saveMode = false)
    {
        $this->executeCallback(function (SchemaTool $tool, array $metaData) use ($saveMode) {
            $tool->updateSchema($metaData, $saveMode);
        }, $generatedFiles, $proxiesDirectory);
    }


    public function dropTable($targetNamespace)
    {
        $chain = $this->entityManager->getConfiguration()->getMetadataDriverImpl()->getDriver();
        $drivers = $chain->getDrivers();

        $dropMetas =  [];
        foreach ($drivers as $namespace => $driver) {
            if ($targetNamespace === $namespace) {
                $allClassNames = $driver->getAllClassNames();

                foreach ($allClassNames as $className) {
                    $dropMetas[] = $this->entityManager->getMetadataFactory()->getMetadataFor($className);
                }
            }
        }
        $tool = new SchemaTool($this->entityManager);
        $tool->dropSchema($dropMetas);
    }
}