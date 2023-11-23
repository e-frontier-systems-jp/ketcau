<?php

namespace Ketcau\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;

abstract class AbstractRepository extends ServiceEntityRepository
{
    protected $ketcauConfig;


    public function delete($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }


    public function save($entity): void
    {
        $this->getEntityManager()->persist($entity);
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