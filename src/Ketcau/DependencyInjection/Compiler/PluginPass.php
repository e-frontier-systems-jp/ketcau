<?php

namespace Ketcau\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //$plugins = $container->getParameter('ketcau.plugins.disabled');

        if (empty($plugins)) {
            $container->log($this, 'disabled plugins not found.');
            return;
        }

        $definitions = $container->getDefinitions();

        foreach ($definitions as $definition) {
            $class = $definition->getClass();
            if (null === $class) {
                continue;
            }
            foreach ($plugins as $plugin) {
                $namespace = 'Plugin\\'. $plugin. '\\';

                if (str_contains($class, $namespace)) {
                    foreach ($definition->getTags() as $tag => $attr) {
                        if ($tag === 'doctrine.repository_service') {
                            continue;
                        }
                        $definition->clearTag($tag);
                    }
                }
            }
        }
    }
}