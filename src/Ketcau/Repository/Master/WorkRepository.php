<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\Work;
use Ketcau\Repository\AbstractRepository;

class WorkRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Work::class);
    }
}