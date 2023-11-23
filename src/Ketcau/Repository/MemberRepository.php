<?php

namespace Ketcau\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Member;

class MemberRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Member::class);
    }
}