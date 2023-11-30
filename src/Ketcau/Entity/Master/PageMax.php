<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(PageMax::class, false)) {

    /**
     * @ORM\Table(name="mtb_page_max")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\PageMaxRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class PageMax extends AbstractMasterEntity
    {
    }
}