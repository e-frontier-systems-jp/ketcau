<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\OrderStatusColor;
use Ketcau\Repository\AbstractRepository;

class OrderStatusColorRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderStatusColor::class);
    }
}
