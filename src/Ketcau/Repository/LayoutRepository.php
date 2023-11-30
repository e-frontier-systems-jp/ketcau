<?php

namespace Ketcau\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Layout;

class LayoutRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Layout::class);
    }
}