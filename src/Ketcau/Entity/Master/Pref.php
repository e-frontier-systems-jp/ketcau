<?php

namespace Ketcau\Entity\Master;

use ApiPlatform\Metadata\ApiResource;
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
    #[ApiResource]
    class Pref extends AbstractMasterEntity
    {
    }
}
