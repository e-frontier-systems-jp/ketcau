<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(BlockPosition::class, false)) {

    /**
     * @ORM\Table(name="dtb_block_position")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\BlockPositionRepository")
     */
    class BlockPosition extends \Ketcau\Entity\AbstractEntity
    {
        /**
         * @var int
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="NONE")
         * @ORM\Column(name="section", type="integer", options={"unsigned": true})
         */
        private $section;

        /**
         * @var int
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="NONE")
         * @ORM\Column(name="block_id", type="integer", options={"unsigned": true})
         */
        private $block_id;

        /**
         * @var int
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="NONE")
         * @ORM\Column(name="layout_id", type="integer", options={"unsigned": true})
         */
        private $layout_id;

        /**
         * @var int
         * @ORM\Column(name="block_row", type="integer", nullable=true, options={"unsigned": true})
         */
        private $block_row;


        /**
         * @var \Ketcau\Entity\Block
         *
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Block", inversedBy="BlockPositions")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="block_id", referencedColumnName="id")
         * })
         */
        private $Block;


        /**
         * @var \Ketcau\Entity\Layout
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Layout", inversedBy="BlockPositions")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
         * })
         */
        private $Layout;




        public function getSection(): int
        {
            return $this->section;
        }

        public function setSection(int $section): BlockPosition
        {
            $this->section = $section;
            return $this;
        }

        public function getBlockId(): int
        {
            return $this->block_id;
        }

        public function setBlockId(int $block_id): BlockPosition
        {
            $this->block_id = $block_id;
            return $this;
        }

        public function getLayoutId(): int
        {
            return $this->layout_id;
        }

        public function setLayoutId(int $layout_id): BlockPosition
        {
            $this->layout_id = $layout_id;
            return $this;
        }

        public function getBlockRow(): int
        {
            return $this->block_row;
        }

        public function setBlockRow(int $block_row): BlockPosition
        {
            $this->block_row = $block_row;
            return $this;
        }

        public function getBlock(): Block
        {
            return $this->Block;
        }

        public function setBlock(Block $Block): BlockPosition
        {
            $this->Block = $Block;
            return $this;
        }

        public function getLayout(): Layout
        {
            return $this->Layout;
        }

        public function setLayout(Layout $Layout): BlockPosition
        {
            $this->Layout = $Layout;
            return $this;
        }
    }
}
