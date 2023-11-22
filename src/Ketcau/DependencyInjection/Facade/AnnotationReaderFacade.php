<?php

namespace Ketcau\DependencyInjection\Facade;

use Doctrine\Common\Annotations\Reader;
use Exception;

class AnnotationReaderFacade
{
    private static AnnotationReaderFacade | null $instance = null;

    private static Reader $Reader;


    public function __construct(Reader $Reader)
    {
        self::$Reader = $Reader;
    }


    public static function init(Reader $Reader): Reader
    {
        if (null === self::$instance) {
            self::$instance = new self($Reader);
        }

        return self::$Reader;
    }

    /**
     * @return Reader
     *
     * @throws Exception
     */
    public static function create(): Reader
    {
        if (null === self::$instance) {
            throw new Exception('Facade is not instantiated');
        }
        return self::$Reader;
    }

    public function getAnnotationReader(): Reader
    {
        return self::$Reader;
    }
}