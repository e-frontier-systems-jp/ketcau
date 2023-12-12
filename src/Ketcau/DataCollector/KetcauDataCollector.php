<?php

namespace Ketcau\DataCollector;

use Ketcau\Common\Constant;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class KetcauDataCollector extends DataCollector
{
    protected $container;


    public function __construct(ContainerInterface $container)
    {
        $this->data = [
            'version' => Constant::VERSION,
            'base_currency_code' => null,
            'currency_code' => null,
            'default_locale_code' => null,
            'locale_code' => null,
            'plugins' => [],
        ];
        $this->container = $container;
    }


    public function getVersion()
    {
        return $this->data['version'];
    }

    public function getBaseCurrencyCode()
    {
        return $this->data['base_currency_code'];
    }

    public function getCurrencyCode()
    {
        return $this->data['currency_code'];
    }

    public function getDefaultLocaleCode()
    {
        return $this->data['default_locale_code'];
    }

    public function getLocaleCode()
    {
        return $this->data['locale_code'];
    }

    public function getPlugins()
    {
        return $this->data['plugins'];
    }


    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data['base_currency_code'] = $this->container->getParameter('currency');
        $this->data['currency_code'] = $this->container->getParameter('currency');

        try {
            $this->data['locale_code'] = $this->container->getParameter('locale');
        } catch (\Exception $exception) {
        }
    }

    public function getName()
    {
        return 'ketcau_core';
    }

    public function reset()
    {
        $this->data = [];
    }
}