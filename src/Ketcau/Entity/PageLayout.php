<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(PageLayout::class, false)) {
    /**
     * @ORM\Table(name="dtb_page_layout")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\PageLayoutRepository")
     */
    class PageLayout extends \Ketcau\Entity\AbstractEntity
    {
        /**
         * @var integer
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="NONE")
         * @ORM\Column(name="page_id", type="integer", options={"unsigned": true})
         */
        private $page_id;

        /**
         * @var integer
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="NONE")
         * @ORM\Column(name="layout_id", type="integer", options={"unsigned": true})
         */
        private $layout_id;

        /**
         * @var integer
         * @ORM\Column(name="sort_no", type="smallint", options={"unsigned": true})
         */
        private $sort_no;

        /**
         * @var \Ketcau\Entity\Page
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Page", inversedBy="PageLayouts")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="page_id", referencedColumnName="id")
         * })
         */
        private $Page;

        /**
         * @var \Ketcau\Entity\Layout
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Layout", inversedBy="PageLayouts")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
         * })
         */
        private $Layout;




        public function getPageId(): int
        {
            return $this->page_id;
        }

        public function setPageId(int $page_id): PageLayout
        {
            $this->page_id = $page_id;
            return $this;
        }

        public function getLayoutId(): int
        {
            return $this->layout_id;
        }

        public function setLayoutId(int $layout_id): PageLayout
        {
            $this->layout_id = $layout_id;
            return $this;
        }

        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        public function setSortNo(int $sort_no): PageLayout
        {
            $this->sort_no = $sort_no;
            return $this;
        }

        public function getPage(): Page
        {
            return $this->Page;
        }

        public function setPage(Page $Page): PageLayout
        {
            $this->Page = $Page;
            return $this;
        }

        public function getLayout(): Layout
        {
            return $this->Layout;
        }

        public function setLayout(Layout $Layout): PageLayout
        {
            $this->Layout = $Layout;
            return $this;
        }



        public function getDeviceTypeId()
        {
            if ($this->Layout->getDeviceType()) {
                return $this->Layout->getDeviceType()->getId();
            }

            return null;
        }
    }
}