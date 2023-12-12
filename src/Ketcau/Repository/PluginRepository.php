<?php

namespace Ketcau\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Plugin;

class PluginRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Plugin::class);
    }

    public function findAllEnabled()
    {
        return $this->findBy(['enabled' => 1]);
    }


    public function findByCode($code)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('LOWER(p.code) = :code')
            ->setParameter('code', strtolower($code));

        return $qb->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}