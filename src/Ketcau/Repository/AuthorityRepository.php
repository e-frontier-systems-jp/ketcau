<?php

namespace Ketcau\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Authority;

class AuthorityRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Authority::class);
    }
}