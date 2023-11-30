<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\OrderItemType;
use Ketcau\Repository\AbstractRepository;

class OrderItemTypeRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderItemType::class);
    }
}