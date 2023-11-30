<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\Country;
use Ketcau\Repository\AbstractRepository;

class CountryRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Country::class);
    }
}
