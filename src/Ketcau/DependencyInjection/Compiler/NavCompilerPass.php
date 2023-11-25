<?php

namespace Ketcau\DependencyInjection\Compiler;

use Ketcau\Common\KetcauNav;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NavCompilerPass implements CompilerPassInterface
{
    public const NAV_TAG = 'ketcau.nav';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $ids = $container->findTaggedServiceIds(self::NAV_TAG);
        $nav = $container->getParameter('ketcau_nav');

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, KetcauNav::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, KetcauNav::class));
            }

            /** @var $class KetcauNav */
            $addNav = $class::getNav();
            $nav = array_replace_recursive($nav, $addNav);
        }

        $container->setParameter('ketcau_nav', $nav);
    }
}