<?php

namespace Customize\Repository;

use Customize\Entity\Seller;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Repository\AbstractRepository;

class SellerRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Seller::class);
    }
}