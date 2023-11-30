<?php

namespace Ketcau\Command;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Ketcau\Service\EntityProxyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateProxyCommand extends Command
{
    protected static $defaultName = 'ketcau:generate:proxies';


    private $entityProxyService;

    private $container;


    public function __construct(EntityProxyService $entityProxyService, ContainerInterface $container)
    {
        parent::__construct();

        $this->entityProxyService = $entityProxyService;
        $this->container = $container;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        AnnotationRegistry::registerAutoloadNamespace('Ketcau\Annotation', __DIR__. '../../src');

        $projectDir = $this->container->getParameter('kernel.project_dir');
        $includeDirs = [$projectDir. '/app/Customize/Entity'];

//        $enabledPlugin = $this->container->getParameter('ketcau.plugins.enabled')

        $this->entityProxyService->generate(
            $includeDirs,
            [],
            $projectDir. '/app/proxy/entity',
            $output
        );

        return 0;
    }
}
