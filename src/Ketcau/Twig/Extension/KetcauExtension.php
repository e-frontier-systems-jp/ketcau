<?php

namespace Ketcau\Twig\Extension;

use Ketcau\Common\KetcauConfig;
use Symfony\Component\Form\FormView;
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
            new TwigFunction('has_errors', [$this, 'hasErrors']),
            new TwigFunction('active_menus', [$this, 'getActiveMenus']),
        ];
    }




    public function hasErrors()
    {
        $hasErrors = false;

        $views = func_get_args();
        foreach ($views as $view) {
            if (!$view instanceof FormView) {
                throw new \InvalidArgumentException();
            }
            if (count($view->vars['errors'])) {
                $hasErrors = true;
                break;
            }
        }

        return $hasErrors;
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