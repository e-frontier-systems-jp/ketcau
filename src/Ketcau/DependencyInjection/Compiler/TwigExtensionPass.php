<?php

namespace Ketcau\DependencyInjection\Compiler;

use Ketcau\Twig\Extension\IgnoreRoutingNotFoundExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigExtensionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter('kernel.debug')) {
            $definition = $container->getDefinition('twig');
            $definition->addMethodCall(
                'addExtension',
                [new Reference(IgnoreRoutingNotFoundExtension::class)]
            );
        }
    }
}