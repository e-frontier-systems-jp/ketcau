<?php

namespace Ketcau\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Layout::class, false)) {

    /**
     * @ORM\Table(name="dtb_layout")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\LayoutRepository")
     */
    class Layout extends AbstractEntity
    {
        public const TARGET_ID_UNUSED = 0;
        public const TARGET_ID_HEAD = 1;
        public const TARGET_ID_BODY_AFTER = 2;
        public const TARGET_ID_HEADER = 3;
        public const TARGET_ID_CONTENTS_TOP = 4;
        public const TARGET_ID_SIDE_LEFT = 5;
        public const TARGET_ID_MAIN_TOP = 6;
        public const TARGET_ID_MAIN_BOTTOM = 7;
        public const TARGET_ID_SIDE_RIGHT = 8;
        public const TARGET_ID_CONTENTS_BOTTOM = 9;
        public const TARGET_ID_FOOTER = 10;
        public const TARGET_ID_DRAWER = 11;
        public const TARGET_ID_CLOSE_BODY_BEFORE = 12;

        /**
         * プレビュー用レイアウト
         */
        public const DEFAULT_LAYOUT_PREVIEW_PAGE = 0;

        /**
         * トップページ用レイアウト
         */
        public const DEFAULT_LAYOUT_TOP_PAGE = 1;

        /**
         * 下層ページ用レイアウト
         */
        public const DEFAULT_LAYOUT_UNDERLAYER_PAGE = 2;


        /**
         * @var integer
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         * @ORM\Column(name="id", type="integer", options={"unsigned": true})
         */
        private $id;

        /**
         * @var string
         * @ORM\Column(name="layout_name", type="string", length=255, nullable=true)
         */
        private $name;

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
         * @var Collection
         * @ORM\OneToMany(targetEntity="Ketcau\Entity\BlockPosition", mappedBy="Layout", cascade={"persist", "remove"})
         */
        private $BlockPositions;

        /**
         * @var Collection
         * @ORM\OneToMany(targetEntity="Ketcau\Entity\PageLayout", mappedBy="Layout", cascade={"persist", "remove"})
         * @ORM\OrderBy({"sort_no" = "ASC"})
         */
        private $PageLayouts;

        /**
         * @var \Ketcau\Entity\Master\DeviceType
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Master\DeviceType")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="device_type_id", referencedColumnName="id")
         * })
         */
        private $DeviceType;


        public function __construct()
        {
            $this->BlockPositions = new ArrayCollection();
            $this->PageLayouts = new ArrayCollection();
        }

        public function getId(): int | null
        {
            return $this->id;
        }

        public function setId(int $id): Layout
        {
            $this->id = $id;
            return $this;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function setName(string $name): Layout
        {
            $this->name = $name;
            return $this;
        }

        public function getCreateDate(): \DateTime
        {
            return $this->create_date;
        }

        public function setCreateDate(\DateTime $create_date): Layout
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getUpdateDate(): \DateTime
        {
            return $this->update_date;
        }

        public function setUpdateDate(\DateTime $update_date): Layout
        {
            $this->update_date = $update_date;
            return $this;
        }

        public function getDeviceType(): Master\DeviceType | null
        {
            return $this->DeviceType;
        }

        public function setDeviceType(Master\DeviceType $DeviceType = null): Layout
        {
            $this->DeviceType = $DeviceType;
            return $this;
        }


        public function addBlockPosition(BlockPosition $BlockPosition): Layout
        {
            $this->BlockPositions[] = $BlockPosition;
            return $this;
        }

        public function removeBlockPosition(BlockPosition $BlockPosition)
        {
            $this->BlockPositions->removeElement($BlockPosition);
        }

        public function getBlockPositions(): Collection
        {
            return $this->BlockPositions;
        }


        public function addPageLayout(PageLayout $PageLayout): Layout
        {
            $this->PageLayouts[] = $PageLayout;
            return $this;
        }

        public function removePageLayout(PageLayout $PageLayout)
        {
            $this->PageLayouts->removeElement($PageLayout);
        }

        public function getPageLayouts(): Collection
        {
            return $this->PageLayouts;
        }


        public function isDefault()
        {
            return in_array($this->id, [
                self::DEFAULT_LAYOUT_PREVIEW_PAGE,
                self::DEFAULT_LAYOUT_TOP_PAGE,
                self::DEFAULT_LAYOUT_UNDERLAYER_PAGE,
            ]);
        }


        public function getPages()
        {
            $Pages = [];
            foreach ($this->PageLayouts as $PageLayout) {
                $Pages[] = $PageLayout->getPage();
            }
            return $Pages;
        }


        public function isDeletable(): bool
        {
            if (!$this->getPageLayouts()->isEmpty()) {
                return false;
            }
            return true;
        }


        public function getBlocks($targetId = null) {
            $TargetBlockPositions = [];

            foreach ($this->BlockPositions as $BlockPosition) {
                if (is_null($targetId)) {
                    $TargetBlockPositions[] = $BlockPosition;
                    continue;
                }

                if ($BlockPosition->getSection() == $targetId) {
                    $TargetBlockPositions[] = $BlockPosition;
                }
            }

            uasort($TargetBlockPositions, function (BlockPosition $a, BlockPosition $b) {
                return ($a->getBlockRow() < $b->getBlockRow()) ? -1 : 1;
            });

            $TargetBlocks = [];
            foreach ($TargetBlockPositions as $BlockPosition) {
                $TargetBlocks[] = $BlockPosition->getBlock();
            }

            return $TargetBlocks;
        }



        public function getBlockPositionsByTargetId($targetId)
        {
            return $this->BlockPositions->filter(
                function ($BlockPosition) use ($targetId) {
                    return $BlockPosition->getSection() == $targetId;
                }
            );
        }

        public function getUnused()
        {
            return $this->getBlocks(self::TARGET_ID_UNUSED);
        }

        public function getHead()
        {
            return $this->getBlocks(self::TARGET_ID_HEAD);
        }

        public function getBodyAfter()
        {
            return $this->getBlocks(self::TARGET_ID_BODY_AFTER);
        }

        public function getHeader()
        {
            return $this->getBlocks(self::TARGET_ID_HEADER);
        }
        public function getContentsTop()
        {
            return $this->getBlocks(self::TARGET_ID_CONTENTS_TOP);
        }

        public function getSideLeft()
        {
            return $this->getBlocks(self::TARGET_ID_SIDE_LEFT);
        }

        public function getMainTop()
        {
            return $this->getBlocks(self::TARGET_ID_MAIN_TOP);
        }

        public function getMainBottom()
        {
            return $this->getBlocks(self::TARGET_ID_MAIN_BOTTOM);
        }

        public function getSideRight()
        {
            return $this->getBlocks(self::TARGET_ID_SIDE_RIGHT);
        }

        public function getContentsBottom()
        {
            return $this->getBlocks(self::TARGET_ID_CONTENTS_BOTTOM);
        }

        public function getFooter()
        {
            return $this->getBlocks(self::TARGET_ID_FOOTER);
        }

        public function getDrawer()
        {
            return $this->getBlocks(self::TARGET_ID_DRAWER);
        }

        public function getCloseBodyBefore()
        {
            return $this->getBlocks(self::TARGET_ID_CLOSE_BODY_BEFORE);
        }


        public function __toString()
        {
            return (string) $this->name;
        }
    }
}