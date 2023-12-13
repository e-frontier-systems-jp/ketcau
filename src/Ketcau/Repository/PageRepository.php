<?php

namespace Ketcau\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Page;

class PageRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Page::class);
    }


    public function getPageByRoute($route)
    {
        $qb = $this->createQueryBuilder('p');

        try {
            $Page = $qb
                ->select(['p', 'p1', 'l'])
                ->leftJoin('p.PageLayouts', 'p1')
                ->leftJoin('p1.Layout', 'l')
                ->where('p.url = :url')
                ->setParameter('url', $route)
                ->getQuery()
                ->setResultCacheLifetime($this->getCacheLifetime())
                ->getSingleResult();
        }
        catch (\Exception $e) {
            return $this->newPage();
        }

        return $Page;
    }


    public function getPageList(?string $where = null, $parameters = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.id <> 0')
            ->andWhere('(p.MasterPage is null OR p.edit_type = :edit_type)')
            ->orderBy('p.id', 'ASC')
            ->setParameter('edit_type', Page::EDIT_TYPE_DEFAULT_CONFIRM);

        if (!is_null($where)) {
            $qb->andWhere($where);
            foreach ($parameters as $key => $val) {
                $qb->setParameter($key, $val);
            }
        }

        return $qb
            ->getQuery()
            ->getResult();
    }


    public function newPage(): Page
    {
        $Page = new Page();
        $Page->setEditType(Page::EDIT_TYPE_USER);
        return $Page;
    }
}