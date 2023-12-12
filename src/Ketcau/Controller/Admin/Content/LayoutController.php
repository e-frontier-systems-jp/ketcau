<?php

namespace Ketcau\Controller\Admin\Content;

use Ketcau\Controller\AbstractController;
use Ketcau\Entity\Layout;
use Ketcau\Form\Type\Admin\LayoutType;
use Ketcau\Repository\BlockPositionRepository;
use Ketcau\Repository\BlockRepository;
use Ketcau\Repository\LayoutRepository;
use Ketcau\Repository\Master\DeviceTypeRepository;
use Ketcau\Repository\PageLayoutRepository;
use Ketcau\Repository\PageRepository;
use Ketcau\Util\CacheUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LayoutController extends AbstractController
{
    protected $blockRepository;

    protected $blockPositionRepository;

    protected $layoutRepository;

    protected $pageLayoutRepository;

    protected $pageRepository;

    protected $deviceTypeRepository;

    private $isPreview = false;


    public function __construct(
        BlockRepository $blockRepository,
        BlockPositionRepository $blockPositionRepository,
        LayoutRepository $layoutRepository,
        PageLayoutRepository $pageLayoutRepository,
        PageRepository $pageRepository,
        DeviceTypeRepository $deviceTypeRepository
    )
    {
        $this->blockRepository = $blockRepository;
        $this->blockPositionRepository = $blockPositionRepository;
        $this->layoutRepository = $layoutRepository;
        $this->pageLayoutRepository = $pageLayoutRepository;
        $this->pageRepository = $pageRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }


    /**
     * @Route("/%ketcau_admin_route%/content/layout", name="admin_content_layout", methods={"GET"})
     */
    #[Template("@admin/content/layout_list.twig")]
    public function index()
    {
        $qb = $this->layoutRepository->createQueryBuilder('l');
        $Layouts = $qb->where('l.id != :DefaultLayoutPreviewPage')
            ->orderBy('l.DeviceType', 'DESC')
            ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
            ->getQuery()
            ->getResult();

        return [
            'Layouts' => $Layouts,
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/content/layout/new", name="admin_content_layout_new", methods={"GET", "POST"})
     * @Route("/%ketcau_admin_route%/content/layout/{id}/edit", requirements={"id" = "\d+"} , name="admin_content_layout_edit", methods={"GET", "POST"})
     */
    #[Template("@admin/Content/layout.twig")]
    public function edit(Request $request, CacheUtil $cacheUtil, $id = null, $previewPageId = null)
    {
        $Layout = null;
        if (is_null($id)) {
            $Layout = new Layout();
        } else {
            $Layout = $this->layoutRepository->get($this->isPreview ? 0 : $id);
            if (is_null($Layout)) {
                throw new NotFoundHttpException();
            }
        }

        // 未使用ブロックの取得
        $Blocks = $Layout->getBlocks();
        $UnusedBlocks = [];
        if (empty($Blocks)) {
            $UnusedBlocks = $this->blockRepository->findAll();
        } else {
            $UnusedBlocks = $this->blockRepository->getUnusedBlocks($Blocks);
        }

        $builder = $this->formFactory->createBuilder(LayoutType::class, $Layout, ['layout_id' => $id]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Layout = $form->getData();
            $this->entityManager->persist($Layout);
            $this->entityManager->flush();

            $BlockPositions = $Layout->getBlockPositions();
            foreach ($BlockPositions as $blockPosition) {
                $Layout->removeBlockPosition($blockPosition);
                $this->entityManager->persist($blockPosition);
                $this->entityManager->flush($blockPosition);
            }

            $data = $request->request->all();
            $this->blockPositionRepository->register($data, $Blocks, $UnusedBlocks, $Layout);

            $cacheUtil->clearDoctrineCache();

            // プレビューモード
            if ($this->isPreview) {

            }

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_content_layout_edit', ['id' => $Layout->getId()]);
        }

        return [
            'form' => $form->createView(),
            'Layout' => $Layout,
            'UnusedBlocks' => $UnusedBlocks,
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/content/layout/{id}/delete", requirements={"id"="\d+"}, name="admin_content_layout_delete", methods={"DELETE"})
     */
    public function delete(Layout $Layout, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        if (!$Layout->isDeletable()) {
            $this->addWarning(trans('admin.common.delete_error_foreign_key', ['%name%' => $Layout->getName()]), 'admin');
            return $this->redirectToRoute('admin_content_layout');
        }

        $this->entityManager->remove($Layout);
        $this->entityManager->flush();

        $this->addSuccess('admin.common.delete_complete', 'admin');

        $cacheUtil->clearDoctrineCache();

        return $this->redirectToRoute('admin_content_layout');
    }
}