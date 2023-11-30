<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\LoginHistoryStatus;
use Ketcau\Repository\AbstractRepository;

class LoginHistoryStatusRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LoginHistoryStatus::class);
    }
}