<?php

namespace Ketcau\Controller\Admin\Content;

use Ketcau\Controller\AbstractController;
use Ketcau\Entity\Layout;
use Ketcau\Repository\BlockPositionRepository;
use Ketcau\Repository\BlockRepository;
use Ketcau\Repository\LayoutRepository;
use Ketcau\Repository\Master\DeviceTypeRepository;
use Ketcau\Repository\PageLayoutRepository;
use Ketcau\Repository\PageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class LayoutController extends AbstractController
{
    protected $blockRepository;

    protected $blockPositionRepository;

    protected $layoutRepository;

    protected $pageLayoutRepository;

    protected $pageRepository;

    protected $deviceTypeRepository;


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
     * @Template("@admin/content/layout_list.twig")
     */
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
}