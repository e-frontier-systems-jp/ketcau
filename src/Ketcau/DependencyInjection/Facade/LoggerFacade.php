<?php

namespace Ketcau\DependencyInjection\Facade;


use Ketcau\Log\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoggerFacade
{
    private static $instance = null;

    private static $Container;

    private static $Logger;


    private function __construct(ContainerInterface $Container, Logger $Logger)
    {
        self::$Container = $Container;
        self::$Logger = $Logger;
    }


    public static function init(ContainerInterface $container, Logger $logger)
    {
        if (null === self::$instance) {
            self::$instance = new self($container, $logger);
        }

        return self::$instance;
    }


    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception('Facade is not instantiated');
        }

        return self::$Logger;
    }


    public static function getLoggerBy($channel)
    {
        return self::$Container->get('monolog.logger.'. $channel);
    }
}