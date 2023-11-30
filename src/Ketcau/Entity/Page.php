<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Page::class, false)) {

    /**
     * @ORM\Table(name="dtb_page", indexes={@ORM\Index(name="dtb_page_url_idx", columns={"url"})})
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\PageRepository")
     */
    class Page extends \Ketcau\Entity\AbstractEntity
    {

        /**
         * @var integer
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         * @ORM\Column(name="id", type="integer", options={"unsigned": true})
         */
        private $id;

        /**
         * @var string|null
         * @ORM\Column(name="page_name", type="string", length=255, nullable=true)
         */
        private $name;

        /**
         * @var string
         * @ORM\Column(name="url", type="string", length=255)
         */
        private $url;

        /**
         * @var string|null
         * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
         */
        private $file_name;

        /**
         * @var int
         * @ORM\Column(name="edit_type", type="smallint", options={"unsigned": true, "default": 1})
         */
        private $edit_type = 1;

        /**
         * @var string|null
         * @ORM\Column(name="author", type="string", length=255, nullable=true)
         */
        private $author;

        /**
         * @var string|null
         * @ORM\Column(name="description", type="string", length=255, nullable=true)
         */
        private $description;

        /**
         * @var string|null
         * @ORM\Column(name="keyword", type="string", length=255, nullable=true)
         */
        private $keyword;

        /**
         * @var \DateTime
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var string|null
         * @ORM\Column(name="meta_robots", type="string", length=255, nullable=true)
         */
        private $meta_robots;

        /**
         * @var string|null
         * @ORM\Column(name="meta_tags", type="string", length=4000, nullable=true)
         */
        private $meta_tags;

        /**
         * @var \Doctrine\Common\Collections\Collection
         * @ORM\OneToMany(targetEntity="Ketcau\Entity\PageLayout", mappedBy="Page", cascade={"persist","remove"})
         */
        private $PageLayouts;

        /**
         * @var \Ketcau\Entity\Page
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Page")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="master_page_id", referencedColumnName="id")
         * })
         */
        private $MasterPage;


        public function __construct()
        {
            $this->PageLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }



        public function getId(): int
        {
            return $this->id;
        }

        public function setId(int $id): Page
        {
            $this->id = $id;
            return $this;
        }

        public function getName(): ?string
        {
            return $this->name;
        }

        public function setName(?string $name): Page
        {
            $this->name = $name;
            return $this;
        }

        public function getUrl(): string
        {
            return $this->url;
        }

        public function setUrl(string $url): Page
        {
            $this->url = $url;
            return $this;
        }

        public function getFileName(): ?string
        {
            return $this->file_name;
        }

        public function setFileName(?string $file_name): Page
        {
            $this->file_name = $file_name;
            return $this;
        }

        public function getEditType(): int
        {
            return $this->edit_type;
        }

        public function setEditType(int $edit_type): Page
        {
            $this->edit_type = $edit_type;
            return $this;
        }

        public function getAuthor(): ?string
        {
            return $this->author;
        }

        public function setAuthor(?string $author): Page
        {
            $this->author = $author;
            return $this;
        }

        public function getDescription(): ?string
        {
            return $this->description;
        }

        public function setDescription(?string $description): Page
        {
            $this->description = $description;
            return $this;
        }

        public function getKeyword(): ?string
        {
            return $this->keyword;
        }

        public function setKeyword(?string $keyword): Page
        {
            $this->keyword = $keyword;
            return $this;
        }

        public function getCreateDate(): \DateTime
        {
            return $this->create_date;
        }

        public function setCreateDate(\DateTime $create_date): Page
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getUpdateDate(): \DateTime
        {
            return $this->update_date;
        }

        public function setUpdateDate(\DateTime $update_date): Page
        {
            $this->update_date = $update_date;
            return $this;
        }

        public function getMetaRobots(): ?string
        {
            return $this->meta_robots;
        }

        public function setMetaRobots(?string $meta_robots): Page
        {
            $this->meta_robots = $meta_robots;
            return $this;
        }

        public function getMetaTags(): ?string
        {
            return $this->meta_tags;
        }

        public function setMetaTags(?string $meta_tags): Page
        {
            $this->meta_tags = $meta_tags;
            return $this;
        }


        public function getPageLayouts(): \Doctrine\Common\Collections\Collection
        {
            return $this->PageLayouts;
        }

        public function addPageLayout(PageLayout $PageLayout): self
        {
            $this->PageLayouts[] = $PageLayout;
            return $this;
        }

        public function removePageLayout(PageLayout $PageLayout)
        {
            $this->PageLayouts->removeElement($PageLayout);
        }

        public function getMasterPage(): Page
        {
            return $this->MasterPage;
        }

        public function setMasterPage(Page $MasterPage): Page
        {
            $this->MasterPage = $MasterPage;
            return $this;
        }



        public function getLayouts()
        {
            $Layouts = [];
            foreach ($this->PageLayouts as $PageLayout) {
                $Layouts[] = $PageLayout->getLayout();
            }
            return $Layouts;
        }


        public function getSorNo($layoutId)
        {
            $pageLayouts = $this->getPageLayouts();
            foreach ($pageLayouts as $pageLayout) {
                if ($pageLayout->getLayoutId() == $layoutId) {
                    return $pageLayout->getSortNo();
                }
            }
            return null;
        }
    }
}