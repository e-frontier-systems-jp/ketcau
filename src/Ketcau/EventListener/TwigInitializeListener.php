<?php

namespace Ketcau\EventListener;

use Ketcau\Common\KetcauConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class TwigInitializeListener implements EventSubscriberInterface
{
    protected bool $initialized = false;

    protected $twig;

    protected $requestContext;


    private $ketcauConfig;

    private $router;


    public function __construct(
        Environment $twig,
        KetcauConfig $ketcauConfig,
        UrlGeneratorInterface $router
    )
    {
        $this->twig = $twig;
        $this->ketcauConfig = $ketcauConfig;
        $this->router = $router;
    }


    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->initialized) {
            return;
        }

        $menus = [];
        $this->twig->addGlobal('menus', $menus);

        $ketcauNav = $this->ketcauConfig['ketcau_nav'];
        $this->twig->addGlobal('ketcauNav', $ketcauNav);

        $this->initialized = true;
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 6],
            ],
        ];
    }
}