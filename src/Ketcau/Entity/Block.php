<?php

namespace Ketcau\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Block::class, false)) {

    /**
     * @ORM\Table(name="dtb_block", uniqueConstraints={@ORM\UniqueConstraint(name="device_type_id", columns={"device_type_id", "file_name"})})
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\BlockRepository")
     */
    class Block extends \Ketcau\Entity\AbstractEntity
    {
        public const UNUSED_BLOCK_ID = 0;


        /**
         * @var int
         * @ORM\Id()
         * @ORM\Column(name="id", type="integer", options={"unsigned": true})
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;


        /**
         * @var string|null
         * @ORM\Column(name="block_name", type="string", length=255, nullable=true)
         */
        private $name;


        /**
         * @var string
         * @ORM\Column(name="file_name", type="string", length=255)
         */
        private $file_name;


        /**
         * @var bool
         * @ORM\Column(name="use_controller", type="boolean", options={"default": false})
         */
        private $use_controller = false;


        /**
         * @var bool
         * @ORM\Column(name="deletable", type="boolean", options={"default": true})
         */
        private $deletable = true;


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
         * @var \Doctrine\Common\Collections\Collection
         * @ORM\OneToMany(targetEntity="Ketcau\Entity\BlockPosition", mappedBy="Block", cascade={"persist","remove"})
         */
        private $BlockPositions;


        /**
         * @var \Ketcau\Entity\Master\DeviceType
         * @ORM\ManyToOne(targetEntity="Ketcau\Entity\Master\DeviceType")
         * @ORM\JoinColumns({
         *     @ORM\JoinColumn(name="device_type_id", referencedColumnName="id")
         * })
         */
        private $DeviceType;


        /**
         *
         */
        public function __construct()
        {
            $this->BlockPositions = new \Doctrine\Common\Collections\ArrayCollection();
        }

        public function getId(): int
        {
            return $this->id;
        }

        public function setId(int $id): self
        {
            $this->id = $id;
            return $this;
        }

        public function getName(): ?string
        {
            return $this->name;
        }

        public function setName(?string $name): self
        {
            $this->name = $name;
            return $this;
        }

        public function getFileName(): string
        {
            return $this->file_name;
        }

        public function setFileName(string $file_name): self
        {
            $this->file_name = $file_name;
            return $this;
        }

        public function isUseController(): bool
        {
            return $this->use_controller;
        }

        public function setUseController(bool $use_controller): self
        {
            $this->use_controller = $use_controller;
            return $this;
        }

        public function isDeletable(): bool
        {
            return $this->deletable;
        }

        public function setDeletable(bool $deletable): self
        {
            $this->deletable = $deletable;
            return $this;
        }

        public function getCreateDate(): \DateTime
        {
            return $this->create_date;
        }

        public function setCreateDate(\DateTime $create_date): self
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getUpdateDate(): \DateTime
        {
            return $this->update_date;
        }

        public function setUpdateDate(\DateTime $update_date): self
        {
            $this->update_date = $update_date;
            return $this;
        }


        public function addBlockPosition(BlockPosition $blockPosition): self
        {
            $this->BlockPositions->add($blockPosition);
            return $this;
        }

        public function removeBlockPosition(BlockPosition $blockPosition): self
        {
            $this->BlockPositions->removeElement($blockPosition);
            return $this;
        }

        public function getBlockPositions(): \Doctrine\Common\Collections\Collection
        {
            return $this->BlockPositions;
        }


        public function setDeviceType(?Master\DeviceType $deviceType): self
        {
            $this->DeviceType = $deviceType;
            return $this;
        }

        public function getDeviceType(): Master\DeviceType
        {
            return $this->DeviceType;
        }
    }
}