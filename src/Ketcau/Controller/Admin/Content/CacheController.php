<?php

namespace Ketcau\Controller\Admin\Content;

use Ketcau\Controller\AbstractController;
use Ketcau\Service\SystemService;
use Ketcau\Util\CacheUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CacheController extends AbstractController
{
    /**
     * @Route("/%ketcau_admin_route%/content/cache", name="admin_content_cache", methods={"GET", "POST"})
     */
    #[Template("@admin/Content/cache.twig")]
    public function index(Request $request, CacheUtil $cacheUtil, SystemService $systemService)
    {
        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $systemService->switchMaintenance(true);

            $cacheUtil->clearCache();

            $this->addFlash('ketcau.admin.disable_maintenance', '');
            $this->addSuccess('admin.common.delete_complete', 'admin');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}