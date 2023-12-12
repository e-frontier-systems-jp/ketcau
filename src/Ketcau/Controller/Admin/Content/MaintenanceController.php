<?php

namespace Ketcau\Controller\Admin\Content;

use Ketcau\Controller\AbstractController;
use Ketcau\Service\SystemService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MaintenanceController extends AbstractController
{
    public function __construct(
        protected SystemService $systemService
    )
    {
    }


    /**
     * @Route("/%ketcau_admin_route%/disable_maintenance/{mode}", requirements={"mode": "manual|auto_maintenance|auto_maintenance_update"}, name="admin_disable_maintenance", methods={"POST"})
     *
     * @param Request $request
     * @param $mode
     * @param SystemService $systemService
     * @return JsonResponse
     */
    public function disableMaintenance(Request $request, $mode, SystemService $systemService): JsonResponse
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