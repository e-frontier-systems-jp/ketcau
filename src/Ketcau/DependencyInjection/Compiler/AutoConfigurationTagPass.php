<?php

namespace Ketcau\DependencyInjection\Compiler;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AutoConfigurationTagPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            $this->configureDoctrineEventSubscriberTag($definition);
            $this->configureRateLimiterTag($id, $definition);
        }
    }


    protected function configureDoctrineEventSubscriberTag(Definition $definition)
    {
        $class = $definition->getClass();
        if (!is_subclass_of($class, EventSubscriber::class)) {
            return;
        }
        if ($definition->hasTag('doctrine.event_subscriber')) {
            return;
        }

        $definition->addTag('doctrine.event_subscriber');
    }


    protected function configureRateLimiterTag($id, Definition $definition)
    {
        if (\str_starts_with($id, 'limiter')
            && $definition instanceof ChildDefinition
            && $definition->getParent() === 'limiter'
            && !$definition->hasTag('ketcau_rate_limiter')
        )
        {
            $definition->addTag('ketcau_rate_limiter');
        }
    }
}