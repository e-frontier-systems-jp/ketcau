<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\PageMax;
use Ketcau\Repository\AbstractRepository;

class PageMaxRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PageMax::class);
    }
}