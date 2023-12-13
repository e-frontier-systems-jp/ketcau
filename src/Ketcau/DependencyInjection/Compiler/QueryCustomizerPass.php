<?php

namespace Ketcau\DependencyInjection\Compiler;

use Ketcau\Doctrine\Query\Queries;
use Ketcau\Doctrine\Query\QueryCustomizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueryCustomizerPass implements CompilerPassInterface
{
    public const QUERY_CUSTOMIZER_TAG = 'ketcau.query_customizer';


    public function process(ContainerBuilder $container): void
    {
        $queries = $container->getDefinition(Queries::class);
        $ids = $container->findTaggedServiceIds(self::QUERY_CUSTOMIZER_TAG);

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, QueryCustomizer::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, QueryCustomizer::class));
            }

            $queries->addMethodCall('addCustomizer', [new Reference($id)]);
        }
    }
}