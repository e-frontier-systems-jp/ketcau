<?php

namespace Ketcau\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists(LoginHistoryStatus::class, false)) {
    /**
     * @ORM\Table(name="mtb_login_history_status")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Ketcau\Repository\Master\LoginHistoryStatusRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class LoginHistoryStatus extends AbstractMasterEntity
    {
        public const FAILURE = 0;

        public const SUCCESS = 1;
    }
}