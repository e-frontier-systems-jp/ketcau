<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(RoundingType::class, false)) {

    /**
     * @ORM\Table(name="mtb_rounding_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\PrefRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class RoundingType extends AbstractMasterEntity
    {
        public const ROUND = 1;


        public const FLOOR = 2;


        public const CEIL = 3;
    }
}