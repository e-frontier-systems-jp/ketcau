<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Pref::class)) {

    /**
     * @ORM\Table(name="mtb_pref")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\PrefRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Pref extends AbstractMasterEntity
    {
    }
}
