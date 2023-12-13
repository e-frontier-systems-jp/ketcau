<?php

namespace Ketcau\EventListener;

use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\Layout;
use Ketcau\Entity\Master\DeviceType;
use Ketcau\Entity\PageLayout;
use Ketcau\Repository\BlockPositionRepository;
use Ketcau\Repository\LayoutRepository;
use Ketcau\Repository\Master\DeviceTypeRepository;
use Ketcau\Repository\PageLayoutRepository;
use Ketcau\Repository\PageRepository;
use Ketcau\Request\Context;
use Ketcau\Service\SystemService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class TwigInitializeListener implements EventSubscriberInterface
{
    protected bool $initialized = false;



    public function __construct(
        protected Environment $twig,
        protected PageRepository $pageRepository,
        protected PageLayoutRepository $pageLayoutRepository,
        protected BlockPositionRepository $blockPositionRepository,
        protected DeviceTypeRepository $deviceTypeRepository,
        protected LayoutRepository $layoutRepository,
        protected SystemService $systemService,
        protected KetcauConfig $ketcauConfig,
        protected Context $requestContext,
        protected UrlGeneratorInterface $router
    )
    {}


    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->initialized) {
            return;
        }

        if ($this->requestContext->isAdmin()) {
            $this->setAdminGlobals($event);
        }
        else {
            $this->setFrontVariables($event);
        }


        $this->initialized = true;
    }


    public function setFrontVariables(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;
        $route = $attributes->get('_route');
        if ($route == 'user_data') {
            $routeParams = $attributes->get('_route_params', []);
            $route = $routeParams['route'] ?? $attributes->get('route', '');
        }

        $type = DeviceType::DEVICE_TYPE_PC;
//        if ($this->mobileDetector->isMobile()) {
//            $type = DeviceType::DEVICE_TYPE_MB;
//        }

        $Page = $this->pageRepository->getPageByRoute($route);
        $PageLayouts = $Page->getPageLayouts();
        $Layout = null;
        /** @var PageLayout $PageLayout */
        foreach ($PageLayouts as $PageLayout) {
            if ($PageLayout->getDeviceTypeId() == $type) {
                $Layout = $PageLayout->getLayout();
                break;
            }
        }

        if (!$Layout) {
            log_info('fallback to PC layout');
            foreach ($PageLayouts as $PageLayout) {
                if ($PageLayout->getDeviceTypeId() == DeviceType::DEVICE_TYPE_PC) {
                    $Layout = $PageLayout->getLayout();
                    break;
                }
            }
        }

        if ($request->get('preview')) {
            $is_admin = $request->getSession()->has('_security_admin');
            if ($is_admin) {
                $Layout = $this->layoutRepository->get(Layout::DEFAULT_LAYOUT_PREVIEW_PAGE);

                $this->twig->addGlobal('Layout', $Layout);
                $this->twig->addGlobal('Page', $Page);
                $this->twig->addGlobal('title', $Page->getName());

                return;
            }
        }

        if ($Layout) {
            $Layout = $this->layoutRepository->get($Layout->getId());
        } else {
            $Layout = new Layout();
        }

        $this->twig->addGlobal('Layout', $Layout);
        $this->twig->addGlobal('Page', $Page);
        $this->twig->addGlobal('title', $Page->getName());
        $this->twig->addGlobal('isMaintenance', $this->systemService->isMaintenanceMode());
    }


    public function setAdminGlobals(RequestEvent $event): void
    {
        $menus = [];
        $this->twig->addGlobal('menus', $menus);

        $ketcauNav = $this->ketcauConfig['ketcau_nav'];
        $this->twig->addGlobal('ketcauNav', $ketcauNav);
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