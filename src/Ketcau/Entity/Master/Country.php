<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(Country::class, false)) {

    /**
     * @ORM\Table(name="mtb_country")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\CountryRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Country extends \Ketcau\Entity\Master\AbstractMasterEntity
    {
    }
}