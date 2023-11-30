<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(OrderStatusColor::class, false)) {

    /**
     * @ORM\Table(name="mtb_order_status_color")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\OrderStatusColorRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class OrderStatusColor extends AbstractMasterEntity
    {
    }
}