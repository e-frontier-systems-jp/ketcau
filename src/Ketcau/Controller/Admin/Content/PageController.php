<?php

namespace Ketcau\Controller\Admin\Content;

use Ketcau\Controller\AbstractController;
use Ketcau\Entity\Layout;
use Ketcau\Entity\Page;
use Ketcau\Entity\PageLayout;
use Ketcau\Form\Type\Admin\MainEditType;
use Ketcau\Repository\Master\DeviceTypeRepository;
use Ketcau\Repository\PageLayoutRepository;
use Ketcau\Repository\PageRepository;
use Ketcau\Util\CacheUtil;
use Ketcau\Util\StringUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class PageController extends AbstractController
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected PageLayoutRepository $pageLayoutRepository,
        protected DeviceTypeRepository $deviceTypeRepository
    ){}


    /**
     * @Route("/%ketcau_admin_route%/content/page", name="admin_content_page", methods={"GET"})
     * @param Request $request
     * @param RouterInterface $router
     * @return array
     */
    #[Template("@admin/Content/page.twig")]
    public function index(Request $request, RouterInterface $router): array
    {
        $Pages = $this->pageRepository->getPageList();

        // TODO: Dispatch Event

        return [
            'Pages' => $Pages,
            'router' => $router,
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/content/page/new", name="admin_content_page_new", methods={"GET", "POST"})
     * @Route("/%ketcau_admin_route%/content/page/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_page_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Environment $twig
     * @param RouterInterface $router
     * @param CacheUtil $cacheUtil
     * @param $id
     * @return
     */
    #[Template("@admin/Content/page_edit.twig")]
    public function edit(Request $request, Environment $twig, RouterInterface $router, CacheUtil $cacheUtil, $id = null)
    {
        $this->addInfoOnce('admin.common.restrict_file_upload_info', 'admin');

        if (null === $id) {
            $Page = $this->pageRepository->newPage();
        } else {
            $Page = $this->pageRepository->find($id);
        }

        $isUserDataPage = false;

        $builder = $this->formFactory
            ->createBuilder(MainEditType::class, $Page);

        // TODO: Event Dispatcher

        $form = $builder->getForm();

        // 更新時
        $fileName = null;
        $namespace = '@user_data/';
        $PrevPage = clone $Page;
        if ($id) {
            if ($Page->getEditType() >= Page::EDIT_TYPE_DEFAULT) {
                $isUserDataPage = false;
                $namespace = '';
            }
            $source = $twig->getLoader()
                ->getSourceContext($namespace. $Page->getFileName(). '.twig')
                ->getCode();

            $form->get('data')->setData($source);

            $fileName = $Page->getFileName();
        }
        elseif ($request->getMethod() === 'GET' && !$form->isSubmitted()) {
            $source = $twig->getLoader()
                ->getSourceContext('@admin/empty_page.twig')
                ->getCode();
            $form->get('data')->setData($source);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Page $Page */
            $Page = $form->getData();

            if (!$isUserDataPage) {
                $Page
                    ->setUrl($PrevPage->getUrl())
                    ->setFileName($PrevPage->getFileName())
                    ->setName($Page->getName());
            }
            // DB登録
            $this->entityManager->persist($Page);
            $this->entityManager->flush();

            // ファイル生成・更新
            if ($isUserDataPage) {
                $templatePath = $this->getParameter('ketcau_theme_user_data_dir');
            } else {
                $templatePath = $this->getParameter('ketcau_theme_front_dir');
            }
            $filePath = $templatePath. '/'. $Page->getFileName(). '.twig';

            $fs = new Filesystem();
            $pageData = $form->get('data')->getData();
            $pageData = StringUtil::convertLineFeed($pageData);
            $fs->dumpFile($filePath, $pageData);

            if ($Page->getFileName() != $fileName && !is_null($fileName)) {
                $oldFilePath = $templatePath. '/'. $fileName. '.twig';
                if ($fs->exists($oldFilePath)) {
                    $fs->remove($oldFilePath);
                }
            }

            foreach ($Page->getPageLayouts() as $PageLayout) {
                $Page->removePageLayout($PageLayout);
                $this->entityManager->remove($PageLayout);
                $this->entityManager->flush();
            }

            /** @var Layout $Layout */
            $Layout = $form['PcLayout']->getData();
            /** @var PageLayout $LastPageLayout */
            $LastPageLayout = $this->pageLayoutRepository->findOneBy([], ['sort_no' => 'DESC']);
            $sortNo = $LastPageLayout->getSortNo();

            if ($Layout) {
                $PageLayout = new PageLayout();
                $PageLayout->setLayoutId($Layout->getId());
                $PageLayout->setLayout($Layout);
                $PageLayout->setPageId($Page->getId());
                $PageLayout->setSortNo($sortNo++);
                $PageLayout->setPage($Page);

                $this->entityManager->persist($PageLayout);
                $this->entityManager->flush();
            }

            $Layout = $form['SpLayout']->getData();
            if ($Layout) {
                $PageLayout = new PageLayout();
                $PageLayout->setLayoutId($Layout->getId());
                $PageLayout->setLayout($Layout);
                $PageLayout->setPageId($Page->getId());
                $PageLayout->setSortNo($sortNo++);
                $PageLayout->setPage($Page);

                $this->entityManager->persist($PageLayout);
                $this->entityManager->flush();
            }

            // TODO: Dispatch Event

            $this->addSuccess('admin.common.save_complete', 'admin');

            $cacheUtil->clearTwigCache();
            $cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('admin_content_page_edit', ['id' => $Page->getId()]);
        }

        if ($isUserDataPage) {
            $templatePath = $this->getParameter('ketcau_theme_user_data_dir');
            $url = '';
        }
        else {
            $templatePath = $this->getParameter('ketcau_theme_front_dir');
            $url = $router->getRouteCollection()->get($PrevPage->getUrl())->getPath();
        }

        $projectDir = $this->getParameter('kernel.project_dir');
        $templatePath = str_replace($projectDir. '/', '', $templatePath);

        return [
            'form' => $form->createView(),
            'page_id' => $Page->getId(),
            'is_user_data_page' => $isUserDataPage,
            'is_confirm_page' => $Page->getEditType() == Page::EDIT_TYPE_DEFAULT_CONFIRM,
            'template_path' => $templatePath,
            'url' => $url,
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/content/page/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_page_delete", methods={"DELETE"})
     * @param Request $request
     * @param CacheUtil $cacheUtil
     * @param $id
     * @return
     */
    public function delete(Request $request, CacheUtil $cacheUtil, $id = null)
    {

    }
}