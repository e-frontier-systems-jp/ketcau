<?php

namespace Ketcau\Controller\Admin\Content;

use Ketcau\Controller\AbstractController;
use Ketcau\Service\SystemService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MaintenanceController extends AbstractController
{
    public function __construct(
        protected SystemService $systemService
    )
    {}


    /**
     * @Route("/%ketcau_admin_route%/content/maintenance", name="admin_content_maintenance", methods={"GET", "POST"})
     * @param Request $request
     * @return array|RedirectResponse
     */
    #[Template("@admin/Content/maintenance.twig")]
    public function index(Request $request): RedirectResponse|array
    {
        $isMaintenance = $this->systemService->isMaintenanceMode();

        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changeTo = $request->request->get('maintenance');
            if ($isMaintenance === false && $changeTo == 'on') {
                $this->systemService->enableMaintenance('', true);
                $this->addSuccess('admin.content.maintenance_switch_on_message', 'admin');
            }
            elseif ($isMaintenance && $changeTo == 'off') {
                $this->systemService->disableMaintenanceNow('', true);
                $this->addSuccess('admin.content.maintenance_switch_off_message', 'admin');
            }

            return $this->redirectToRoute('admin_content_maintenance');
        }

        return [
            'form' => $form->createView(),
            'isMaintenance' => $isMaintenance,
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/disable_maintenance/{mode}", requirements={"mode": "manual|auto_maintenance|auto_maintenance_update"}, name="admin_disable_maintenance", methods={"POST"})
     *
     * @param Request $request
     * @param string $mode
     * @param SystemService $systemService
     * @return JsonResponse
     */
    public function disableMaintenance(Request $request, string $mode, SystemService $systemService): JsonResponse
    {
        $this->isTokenValid();

        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        if ($mode === 'manual') {
            $path = $this->getParameter('ketcau_content_maintenance_file_path');
            if (file_exists($path)) {
                unlink($path);
            }
        }
        else {
            $maintenanceMode = [
                'auto_maintenance' => SystemService::AUTO_MAINTENANCE,
                'auto_maintenance_update' => SystemService::AUTO_MAINTENANCE_UPDATE,
            ];
            $systemService->disableMaintenance($maintenanceMode[$mode]);
        }

        return $this->json(['success' => true]);
    }
}