<?php

namespace Ketcau;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Ketcau\Common\KetcauNav;
use Ketcau\DependencyInjection\Compiler\NavCompilerPass;
use Ketcau\DependencyInjection\Facade\TranslatorFacade;
use Ketcau\Doctrine\DBAL\Types\UTCDateTimeType;
use Ketcau\Doctrine\DBAL\Types\UTCDateTimeTzType;
use Ketcau\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Ketcau\Twig\Extension\KetcauExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';


    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->loadEntityProxies();
    }


    public function boot()
    {
        parent::boot();

        $container = $this->getContainer();

        $timezone = $container->getParameter('timezone');
        UTCDateTimeType::setTimezone($timezone);
        UTCDateTimeTzType::setTimezone($timezone);

        date_default_timezone_set($timezone);

        $Translator = $container->get('translator');
        if ($Translator !== null && $Translator instanceof \Symfony\Contracts\Translation\TranslatorInterface) {
            TranslatorFacade::init($Translator);
        }
    }


    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $confDir = $this->getProjectDir(). '/config/';
        $loader->load($confDir. '/services'. self::CONFIG_EXTS, 'glob');
        $loader->load($confDir. '/packages/*'. self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir. '/packages/'. $this->environment)) {
            $loader->load($confDir. '/packages/'. $this->environment. '/**/*'. self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services_'.$this->environment. self::CONFIG_EXTS, 'glob');
    }

    protected function build(ContainerBuilder $container): void
    {
        $this->addEntityExtensionPass($container);

        //$container->registerExtension(new KetcauExtension($container->get('KetcauConfig')));

        $container->registerForAutoconfiguration(KetcauNav::class)
            ->addTag(NavCompilerPass::NAV_TAG);
        $container->addCompilerPass(new NavCompilerPass());
    }


    protected function addEntityExtensionPass(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        $paths = ['%kernel.project_dir%/Ketcau/Entity'];
        $namespaces = ['Ketcau\\Entity'];
        $reader = new Reference('annotation_reader');
        $driver = new Definition(AnnotationDriver::class, [$reader, $paths]);
        $driver->addMethodCall('setTraitProxiesDirectory', [$projectDir. '/app/proxy/entity']);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, $namespaces, []));
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
