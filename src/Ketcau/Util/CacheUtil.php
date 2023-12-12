<?php

namespace Ketcau\Util;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class CacheUtil implements EventSubscriberInterface
{
    public const DOCTRINE_APP_CACHE_KEY = 'doctrine.app_cache_pool';

    private $clearCacheAfterResponse = false;


    protected $kernel;

    private $container;


    public function __construct(KernelInterface $kernel, ContainerInterface $container)
    {
        $this->kernel = $kernel;
        $this->container = $container;
    }



    public function clearCache($env = null)
    {
        $this->clearCacheAfterResponse = $env;
    }


    public function forceClearCache(TerminateEvent $event)
    {
        if ($this->clearCacheAfterResponse === false) {
            return;
        }

        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $command = [
            'command' => 'cache:clear',
            '--no-warmup' => true,
            '--no-ansi' => true,
        ];

        if ($this->clearCacheAfterResponse !== null) {
            $command['--env'] = $this->clearCacheAfterResponse;
        }

        $input = new ArrayInput($command);

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache('user');
            apc_clear_cache();
        }

        if (function_exists('wincache_ucache_clear')) {
            wincache_ucache_clear();
        }

        return $output->fetch();
    }


    /**
     * Doctrineのキャッシュを削除します
     */
    public function clearDoctrineCache()
    {
        $poolClearer = $this->container->get('cache.global_clearer');
        if (!$poolClearer->hasPool(self::DOCTRINE_APP_CACHE_KEY)) {
            return;
        }

        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $command = [
            'command' => 'cache:pool:clear',
            'pools' => [self::DOCTRINE_APP_CACHE_KEY],
            '--no-ansi' => true,
        ];

        $input = new ArrayInput($command);
        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        return $output->fetch();
    }


    /**
     * Twigキャッシュを削除します。
     */
    public function clearTwigCache()
    {
        $cacheDir = $this->kernel->getCacheDir(). '/twig';
        $fs = new Filesystem();
        $fs->remove($cacheDir);
    }


    public static function getSubscribedEvents()
    {
        return [KernelEvents::TERMINATE => 'forceClearCache'];
    }
}