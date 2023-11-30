<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="mtb_order_item_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\OrderItemTypeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class OrderItemType extends AbstractMasterEntity
{
    public const PRODUCT = 1;

    public const DELIVERY_FREE = 2;

    public const CHARGE = 4;

    public const DISCOUNT = 5;

    public const TAX = 5;

    public const POINT = 6;


    public function isProduct()
    {
        return ($this->id == self::PRODUCT);
    }
}