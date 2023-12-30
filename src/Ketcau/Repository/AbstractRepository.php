<?php

namespace Ketcau\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Ketcau\Common\KetcauConfig;

abstract class AbstractRepository extends ServiceEntityRepository
{
    protected ?KetcauConfig $ketcauConfig = null;



    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }


    protected function removeEntity($entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    protected function saveEntity(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    protected function getCacheLifetime(): int
    {
        if($this->ketcauConfig !== null) {
            return $this->ketcauConfig['ketcau_result_cache_life_time'];
        }

        return 0;
    }


    /**
     * @return bool
     * @throws Exception
     */
    protected function isPostgreSQL(): bool
    {
        return 'postgresql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function isMySQL(): bool
    {
        return 'mysql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }
}