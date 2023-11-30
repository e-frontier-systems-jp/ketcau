<?php

namespace Ketcau\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\Master\RoundingType;
use Ketcau\Repository\AbstractRepository;

class RoundingTypeRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registryRounding)
    {
        parent::__construct($registryRounding, RoundingType::class);
    }
}