<?php

namespace Ketcau\Service\Composer;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ComposerServiceFactory
{
    public static function createService(ContainerInterface $container)
    {
        return $container->get(ComposerApiService::class);
    }
}