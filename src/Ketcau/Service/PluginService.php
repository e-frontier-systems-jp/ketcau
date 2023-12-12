<?php

namespace Ketcau\Service;

use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Common\KetcauConfig;
use Ketcau\Repository\PluginRepository;
use Ketcau\Service\Composer\ComposerServiceInterface;
use Ketcau\Util\CacheUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginService
{
    protected $entityManager;

    protected $pluginRepository;

    protected $entityProxyService;

    protected $schemaService;

    protected $ketcauConfig;

    protected $container;

    protected $cacheUtil;

    protected $composerService;

    protected $systemService;

    protected $pluginContext;


    private $projectRoot;

    private $environment;


    public function __construct(
        EntityManagerInterface $entityManager,
        PluginRepository $pluginRepository,
        EntityProxyService $entityProxyService,
        SchemaService $schemaService,
        KetcauConfig $ketcauConfig,
        ContainerInterface $container,
        CacheUtil $cacheUtil,
        ComposerServiceInterface $composerService,
        SystemService $systemService,
        PluginContext $pluginContext
    )
    {
        $this->entityManager = $entityManager;
        $this->pluginRepository = $pluginRepository;
        $this->entityProxyService = $entityProxyService;
        $this->schemaService = $schemaService;
        $this->ketcauConfig = $ketcauConfig;
        $this->container = $container;
        $this->cacheUtil = $cacheUtil;
        $this->composerService = $composerService;
        $this->systemService = $systemService;
        $this->pluginContext = $pluginContext;

        $this->projectRoot = $ketcauConfig->get('kernel.project_dir');
        $this->environment = $ketcauConfig->get('kernel.environment');
    }
}