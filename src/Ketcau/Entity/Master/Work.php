<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Work::class, false)) {

    /**
     * Work
     *
     * @ORM\Table(name="mtb_work")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\WorkRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Work extends \Ketcau\Entity\Master\AbstractMasterEntity
    {
        /**
         * 非稼働
         */
        public const NON_ACTIVE = 0;

        /**
         * 稼働
         */
        public const ACTIVE = 1;
    }
}