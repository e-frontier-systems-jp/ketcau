<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(OrderStatus::class, false)) {

    /**
     * @ORM\Table(name="mtb_order_status")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\OrderStatusRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class OrderStatus extends AbstractMasterEntity
    {
        public const NEW = 1;

        public const CANCEL = 3;

        public const IN_PROGRESS = 4;

        public const DELIVERED = 5;

        public const PAID = 6;

        public const PENDING = 7;

        public const PROCESSING = 8;

        public const RETURNED = 9;


        /**
         * @var bool
         * @ORM\Column(name="display_order_count", type="boolean", options={"default":false})
         */
        private $display_order_count = false;

        public function isDisplayOrderCount(): bool
        {
            return $this->display_order_count;
        }

        public function setDisplayOrderCount(bool $display_order_count): OrderStatus
        {
            $this->display_order_count = $display_order_count;
            return $this;
        }
    }
}