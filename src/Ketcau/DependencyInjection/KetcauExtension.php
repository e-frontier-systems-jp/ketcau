<?php

namespace Ketcau\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Configuration as DoctrineBundleConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KetcauExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);
    }

    public function getAlias(): string
    {
        return 'ketcau';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return parent::getConfiguration($config, $container);
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->configureFramework($container);

    }

    protected function configureFramework(ContainerBuilder $container)
    {
        $forceSSL = $container->resolveEnvPlaceholders('%env(KETCAU_FORCE_SSL)%', true);

        if ('1' === $forceSSL) {
            $forceSSL = true;
        } elseif ('0' === $forceSSL) {
            $forceSSL = false;
        }

        $accessControl = [
            ['path' => '^/%ketcau_admin_route%/login', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['path' => '^/%ketcau_admin_route%/', 'roles' => 'ROLE_ADMIN'],
        ];
//        if ($forceSSL) {
//            foreach ($accessControl as &$control) {
//                $control['require_channel'] = 'https';
//            }
//        }

        $container->prependExtensionConfig('security', [
            'access_control' => $accessControl,
        ]);

        $configs = $container->getExtensionConfig('ketcau');
        $configs = array_reverse($configs);
        $rateLimiterConfigs = [];

        foreach ($configs as $config) {
            if (empty($config['rate_limiter'])) {
                continue;
            }

            foreach ($config['rate_limiter'] as $id => $limiter) {
                $container->prependExtensionConfig('framework', [
                    'rate_limiter' => [
                        $id => [
                            'policy' => 'fixed_window',
                            'limit' => $limiter['limit'],
                            'interval' => $limiter['interval'],
                            'cache_pool' => 'rate_limiter.cache',
                        ],
                    ],
                ]);

                if (isset($limiter['route']) && !isset($rateLimiterConfigs[$limiter['route']][$id])) {
                    $processor = new Processor();
                    $configuration = new Configuration();
                    $processed = $processor->processConfiguration($configuration, ['ketcau' => ['late_limiter' => [$id => $limiter]]]);
                    $rateLimiterConfigs[$limiter['route']][$id] = $processed['route_limiter']['limiters'][$id];
                }
            }
        }

        $container->setParameter('ketcau_rate_limiter_configs', $rateLimiterConfigs);
    }


    protected function configurePlugin(ContainerBuilder $container)
    {
        $pluginDir = $container->getParameter('kernel.project_dir'). '/app/Plugin';
        $pluginDirs = $this->getPluginDirectories($pluginDir);

        $container->setParameter('ketcau.plugins.enabled', []);
        $container->setParameter('ketcau.plugins.disabled', $pluginDirs);

        $configs = $container->getExtensionConfig('doctrine');
        $configs = $container->resolveEnvPlaceholders($configs, true);

        $configuration = new DoctrineBundleConfiguration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        $params = $config['dbal']['connections'][$config['dbal']]['default_connection'];
        $params['url'] = env('DATABASE_URL');
        $connection = DriverManager::getConnection($params);
        if (!$this->isConnected($connection)) {
            return;
        }

        $stmt = $connection->executeQuery('SELECT * FROM dtb_plugin');
        $plugins = $stmt->fetchAllAssociative();

        $enabled = [];
        foreach ($plugins as $plugin) {
            if (array_key_exists('enabled', $plugin) && $plugin['enabled']) {
                $enabled[] = $plugin['code'];
            }
        }

        $disabled = [];
        foreach ($pluginDirs as $dir) {
            if (!in_array($dir, $enabled)) {
                $disabled[] = $dir;
            }
        }

        $container->setParameter('ketcau.plugins.enabled', $enabled);
        $container->setParameter('ketcau.plugins.disabled', $disabled);

        $this->configureTwigPaths($container, $enabled, $pluginDir);
        $this->configureTranslations($container, $enabled, $pluginDir);
    }


    protected function configureTwigPaths(ContainerBuilder $container, $enabled, $pluginDir)
    {
        $paths = [];
        $projectDir = $container->getParameter('kernel.project_dir');

        foreach ($enabled as $code) {
            $dir = $projectDir. '/app/template/plugin/'. $code;
            if (file_exists($dir)) {
                $paths[$dir] = $code;
            }

            $dir = $pluginDir. '/'. $code. '/Resource/template';
            if (file_exists($dir)) {
                $paths[$dir] = $code;
            }
        }

        if (!empty($paths)) {
            $container->prependExtensionConfig('twig', [
                'paths' => $paths,
            ]);
        }
    }


    protected function configureTranslations(ContainerBuilder $container, $enabled, $pluginDir)
    {
        $paths = [];

        foreach ($enabled as $code) {
            $dir = $pluginDir. '/'. $code. '/Resource/locale';
            if (file_exists($dir)) {
                $paths[] = $dir;
            }
        }

        if (!empty($paths)) {
            $container->prependExtensionConfig('framework', [
                'translator' => [
                    'paths' => $paths,
                ],
            ]);
        }
    }


    protected function isConnected(Connection $connection)
    {
        try {
            if (!$connection->executeQuery('SELECT 1')) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        $tableNames = $connection->createSchemaManager()->listTableNames();

        return in_array('dtb_plugin', $tableNames);
    }


    protected function getPluginDirectories($pluginDir)
    {
        $finder = (new Finder())
            ->in($pluginDir)
            ->sortByName()
            ->depth(0)
            ->directories();

        $dirs = [];
        foreach ($finder as $dir) {
            $dirs[] = $dir->getBasename();
        }

        return $dirs;
    }
}