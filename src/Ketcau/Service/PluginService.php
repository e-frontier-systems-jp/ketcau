<?php

namespace Ketcau\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\Plugin;
use Ketcau\Repository\PluginRepository;
use Ketcau\Service\Composer\ComposerServiceInterface;
use Ketcau\Util\CacheUtil;
use Ketcau\Util\StringUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PluginService
{
    private $projectRoot;

    private $environment;


    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected PluginRepository $pluginRepository,
        protected EntityProxyService $entityProxyService,
        protected SchemaService $schemaService,
        protected KetcauConfig $ketcauConfig,
        protected ContainerInterface $container,
        protected CacheUtil $cacheUtil,
        protected ComposerServiceInterface $composerService,
        protected SystemService $systemService,
        protected PluginContext $pluginContext
    )
    {
        $this->projectRoot = $ketcauConfig->get('kernel.project_dir');
        $this->environment = $ketcauConfig->get('kernel.environment');
    }


    public function generateProxyAndCallback(callable $callback, Plugin $Plugin, $config, $uninstall = false, $tempProxyOutputDir = null)
    {
        if ($Plugin->isEnabled()) {
            $generatedFiles = $this->regenerateProxy($Plugin, false, $tempProxyOutputDir ?: $this->projectRoot. '/app/proxy/entity');
            call_user_func($callback, $generatedFiles, $tempProxyOutputDir ?: $this->projectRoot. '/app/proxy/entity');
        } else {
            $createOutputDir = false;
            if (is_null($tempProxyOutputDir)) {
                $tempProxyOutputDir = sys_get_temp_dir(). '/proxy_'. StringUtil::random(12);
                @mkdir($tempProxyOutputDir);
                $createOutputDir = true;
            }

            try {
                if (!$uninstall) {
                    $entityDir = $this->ketcauConfig['plugin_realdir']. '/'. $Plugin->getCode(). '/Entity';
                    if (file_exists($entityDir)) {
                        $ormConfig = $this->entityManager->getConfiguration();
                        $namespace = 'Plugin\\'. $config['code']. '\\Entity';
                        $ormConfig->setEntityNamespaces([$Plugin->getCode() => $namespace]);
                    }
                }

                $generatedFiles = $this->regenerateProxy($Plugin, true, $tempProxyOutputDir, $uninstall);

                call_user_func($callback, $generatedFiles, $tempProxyOutputDir);
            }
            finally {
                if ($createOutputDir) {
                    $files = Finder::create()
                        ->in($tempProxyOutputDir)
                        ->files();
                    $f = new Filesystem();
                    $f->remove($files);
                }
            }
        }
    }



    private function regenerateProxy(Plugin $Plugin, $temporary, $outputDir = null, $uninstall = false)
    {
        if (is_null($outputDir)) {
            $outputDir = $this->projectRoot. '/app/proxy/entity';
        }
        @mkdir($outputDir);

        $enabledPluginCodes = array_map(
            function ($p) { return $p->getCode(); },
            $temporary ? $this->pluginRepository->findAll() : $this->pluginRepository->findAllEnabled()
        );

        $excludes = [];
        if (!$uninstall && ($temporary || $Plugin->isEnabled())) {
            $enabledPluginCodes[] = $Plugin->getCode();
        } else {
            $index = array_search($Plugin->getCode(), $enabledPluginCodes);
            if ($index !== false && $index >= 0) {
                array_splice($enabledPluginCodes, $index, 1);
                $excludes = [$this->projectRoot. '/app/Plugin/'. $Plugin->getCode(). '/Entity'];
            }
        }

        $enabledPluginEntityDirs = array_map(function ($code) {
            return $this->projectRoot. "/app/Plugin/${code}/Entity";
        }, $enabledPluginCodes);

        return $this->entityProxyService->generate(
            array_merge([$this->projectRoot. '/app/Customize/Entity'], $enabledPluginEntityDirs),
            $excludes,
            $outputDir
        );
    }
}