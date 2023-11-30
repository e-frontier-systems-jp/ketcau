<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(DeviceType::class, false)) {

    /**
     * @ORM\Table(name="mtb_device_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\DeviceTypeRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class DeviceType extends \Ketcau\Entity\Master\AbstractMasterEntity
    {
        public const DEVICE_TYPE_MB = 2;
        public const DEVICE_TYPE_PC = 10;
    }
}