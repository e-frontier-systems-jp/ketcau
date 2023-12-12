<?php

namespace Ketcau;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Ketcau\Common\KetcauNav;
use Ketcau\DependencyInjection\Compiler\AutoConfigurationTagPass;
use Ketcau\DependencyInjection\Compiler\NavCompilerPass;
use Ketcau\DependencyInjection\Compiler\PluginPass;
use Ketcau\DependencyInjection\Compiler\QueryCustomizerPass;
use Ketcau\DependencyInjection\Compiler\TwigExtensionPass;
use Ketcau\DependencyInjection\Facade\AnnotationReaderFacade;
use Ketcau\DependencyInjection\Facade\LoggerFacade;
use Ketcau\DependencyInjection\Facade\TranslatorFacade;
use Ketcau\DependencyInjection\KetcauExtension;
use Ketcau\Doctrine\DBAL\Types\UTCDateTimeType;
use Ketcau\Doctrine\DBAL\Types\UTCDateTimeTzType;
use Ketcau\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Ketcau\Doctrine\Query\QueryCustomizer;
use Ketcau\Log\Logger;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Contracts\Translation\TranslatorInterface;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';


    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->loadEntityProxies();
    }


    public function getCacheDir(): string
    {
        return $this->getProjectDir(). '/var/cache/'. $this->environment;
    }


    public function getLogDir(): string
    {
        return $this->getProjectDir(). '/var/log';
    }


    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir(). '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class;
            }
        }

        $pluginDir = $this->getProjectDir(). '/app/Plugin';
        $finder = (new Finder())
            ->in($pluginDir)
            ->sortByName()
            ->depth(0)
            ->directories();
        $plugins = array_map(function ($dir) {
            return $dir->getBaseName();
        }, iterator_to_array($finder));

        foreach ($plugins as $code) {
            $pluginBundles = $pluginDir. '/'. $code. '/Resource/config/bundles.php';
            if (file_exists($pluginBundles)) {
                $contents = require $pluginBundles;
                foreach ($contents as $class => $envs) {
                    if (isset($envs['all']) || isset($envs[$this->environment])) {
                        yield new $class;
                    }
                }
            }
        }
    }


    public function boot(): void
    {
        parent::boot();

        $container = $this->getContainer();

        $timezone = $container->getParameter('timezone');
        UTCDateTimeType::setTimezone($timezone);
        UTCDateTimeTzType::setTimezone($timezone);

        date_default_timezone_set($timezone);

        $Logger = $container->get('ketcau.logger');
        if ($Logger instanceof Logger) {
            LoggerFacade::init($container, $Logger);
        }

        $Translator = $container->get('translator');
        if ($Translator instanceof TranslatorInterface) {
            TranslatorFacade::init($Translator);
        }

        $AnnotationReaderFacade = $container->get(AnnotationReaderFacade::class);
        $AnnotationReader = $AnnotationReaderFacade->getAnnotationReader();
        AnnotationReaderFacade::init($AnnotationReader);
    }


    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir(). '/config';

        $loader->load($confDir. '/services'. self::CONFIG_EXTS, 'glob');
        $loader->load($confDir. '/packages/*'. self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir. '/packages/'. $this->environment)) {
            $loader->load($confDir. '/packages/'. $this->environment. '/**/*'. self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services_'. $this->environment. self::CONFIG_EXTS, 'glob');

        // プラグインのservices.phpをロードする
        $dir = dirname(__DIR__). '/../app/Plugin/*/Resource/config';
        $loader->load($dir. '/services'. self::CONFIG_EXTS, 'glob');
        $loader->load($dir. '/services_'. $this->environment. self::CONFIG_EXTS, 'glob');

        // カスタマイズディレクトリのservices.phpをロードする
        $dir = dirname(__DIR__). '/../app/Customize/Resource/config';
        $loader->load($dir. '/services'. self::CONFIG_EXTS, 'glob');
        $loader->load($dir. '/services_'. $this->environment. self::CONFIG_EXTS, 'glob');
    }


//    protected function configureRoutes(RouteCollection $routes): void
//    {
//        $container = $this->getContainer();
//
//        $scheme =['https', 'http'];
//        $forceSSL = $container->getParameter('ketcau_force_ssl');
//        if ($forceSSL) {
//            $scheme = ['https'];
//        }
//        // $routes->schema
//
//        $confDir = $this->getProjectDir(). '/config';
//
//        if (is_dir($confDir. '/routes/')) {
//            $builder = $routes->import($confDir. '/routes/*'. self::CONFIG_EXTS);
//            $builder->schemes($scheme);
//        }
//        if (is_dir($confDir. '/routes/'. $this->environment))
//        {
//            $builder = $routes->import($confDir. '/routes/'. $this->environment. '/**/*'. self::CONFIG_EXTS);
//            $builder->schemes($scheme);
//        }
//
//        $builder = $routes->import($confDir. '/routes'. self::CONFIG_EXTS);
//        $builder->schemes($scheme);
//        $builder = $routes->import($confDir. '/routes_'. $this->environment. self::CONFIG_EXTS);
//        $builder->schemes($scheme);
//
//        // 有効なプラグインのルーティングをインポートする
//        $plugins = $container->getParameter('ketcau.plugins.enabled');
//        $pluginDir = $this->getProjectDir(). '/app/Plugin';
//        foreach ($plugins as $plugin) {
//            $dir = $pluginDir. '/'. $plugin. '/Controller';
//            if (file_exists($dir)) {
//                $builder = $routes->import($dir, 'annotation');
//                $builder->schemes($scheme);
//            }
//            if (file_exists($pluginDir. '/'. $plugin. '/Resource/config')) {
//                $builder = $routes->import($pluginDir. '/'. $plugin. '/Resource/config/routes'. self::CONFIG_EXTS, 'glob');
//                $builder->schemes($scheme);
//            }
//        }
//    }


    protected function build(ContainerBuilder $container): void
    {
        $this->addEntityExtensionPass($container);

        $container->registerExtension(new KetcauExtension());

        $container->addCompilerPass(new AutoConfigurationTagPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 11);

        $container->addCompilerPass(new PluginPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);

        $container->addCompilerPass(new TwigExtensionPass());

        $container->registerForAutoconfiguration(QueryCustomizer::class)
            ->addTag(QueryCustomizerPass::QUERY_CUSTOMIZER_TAG);
        $container->addCompilerPass(new QueryCustomizerPass());

        $container->registerForAutoconfiguration(KetcauNav::class)
            ->addTag(NavCompilerPass::NAV_TAG);
        $container->addCompilerPass(new NavCompilerPass());
    }


    protected function addEntityExtensionPass(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        $paths = ['%kernel.project_dir%/src/Ketcau/Entity'];
        $namespaces = ['Ketcau\\Entity'];
        $reader = new Reference('annotation_reader');
        $driver = new Definition(AnnotationDriver::class, [$reader, $paths]);
        $driver->addMethodCall('setTraitProxiesDirectory', [$projectDir. '/app/proxy/entity']);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, $namespaces, []));

        $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver(
            ['Customize\\Entity'],
            ['%kernel.project_dir%/app/Customize/Entity']
        ));

    }


    protected function loadEntityProxies(): void
    {
        if (true === $this->booted) {
            return;
        }

        $files = Finder::create()
            ->in(__DIR__. '/../../app/proxy/entity/')
            ->name('*.php')
            ->files();

        foreach($files as $file) {
            require_once $file->getRealPath();
        }
    }
}
