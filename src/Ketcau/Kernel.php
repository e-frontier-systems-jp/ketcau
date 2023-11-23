<?php

namespace Ketcau;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Ketcau\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;


    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->loadEntityProxies();
    }


    protected function build(ContainerBuilder $container): void
    {
        $this->addEntityExtensionPass($container);
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
