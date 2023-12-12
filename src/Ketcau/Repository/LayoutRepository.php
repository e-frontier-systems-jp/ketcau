<?php

namespace Ketcau\Repository;

use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Layout;

class LayoutRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Layout::class);
    }


    public function get($id)
    {
        try {
            $Layout = $this->createQueryBuilder('l')
                ->select('l')
                ->leftJoin('l.BlockPositions', 'bp')
                ->leftJoin('bp.Block', 'b')
                ->where('l.id = :id')
                ->orderBy('bp.block_row', 'ASC')
                ->setParameter('id', $id)
                ->getQuery()
                ->useQueryCache(true)
                ->getSingleResult();
        }
        catch (NoResultException $e) {
            return null;
        }

        return $Layout;
    }
}