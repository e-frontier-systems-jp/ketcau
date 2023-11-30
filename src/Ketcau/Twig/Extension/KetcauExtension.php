<?php

namespace Ketcau\Twig\Extension;

use Ketcau\Common\KetcauConfig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class KetcauExtension extends AbstractExtension
{
    protected $ketcauConfig;


    /**
     * @param KetcauConfig $ketcauConfig
     */
    public function __construct(KetcauConfig $ketcauConfig)
    {
        $this->ketcauConfig = $ketcauConfig;
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_menus', [$this, 'getActiveMenus']),
        ];
    }




    public function getActiveMenus($menus = [])
    {
        $count = count($menus);
        for ($i = $count; $i <= 2; $i++) {
            $menus[] = '';
        }

        return $menus;
    }
}