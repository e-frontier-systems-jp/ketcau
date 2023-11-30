<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\DeviceType;
use Ketcau\Repository\AbstractRepository;

class DeviceTypeRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeviceType::class);
    }
}