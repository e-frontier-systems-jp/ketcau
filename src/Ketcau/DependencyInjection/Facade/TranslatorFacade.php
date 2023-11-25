<?php

namespace Ketcau\DependencyInjection\Facade;

use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorFacade
{
    private static $instance = null;

    private static TranslatorInterface $Translator;


    private function __construct(TranslatorInterface $Translator)
    {
        self::$Translator = $Translator;
    }


    public static function init(TranslatorInterface $Translator)
    {
        if (null === self::$instance) {
            self::$instance = new self($Translator);
        }

        return self::$instance;
    }


    public static function create()
    {
        if (null === self::$instance) {
            throw new \Exception('Facade is not instantiated');
        }

        return self::$Translator;
    }
}
