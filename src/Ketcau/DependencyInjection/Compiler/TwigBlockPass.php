<?php

namespace Ketcau\DependencyInjection\Compiler;

use Ketcau\Common\KetcauTwigBlock;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigBlockPass implements CompilerPassInterface
{
    public const TWIG_BLOCK_TAG = 'ketcau.twig_block';


    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds(self::TWIG_BLOCK_TAG);
        $templates = $container->getParameter('ketcau_twig_block_templates');

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, KetcauTwigBlock::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s"', $id, KetcauTwigBlock::class));
            }

            $blocks = $class::getTwigBlock();
            foreach ($blocks as $block) {
                $templates[] = $block;
            }
        }
        $container->setParameter('ketcau_twig_block_templates', $templates);
    }
}