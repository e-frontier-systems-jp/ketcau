<?php

namespace Ketcau\Doctrine\Query;

use Doctrine\ORM\QueryBuilder;

class Queries
{
    private array $customizers = [];


    public function addCustomizer(QueryCustomizer $customizer): void
    {
        $queryKey = $customizer->getQueryKey();
        $this->customizers[$queryKey][] = $customizer;
    }


    public function customize($queryKey, QueryBuilder $builder, $params): QueryBuilder
    {
        if (isset($this->customizers[$queryKey])) {
            foreach ($this->customizers[$queryKey] as $customizer) {
                $customizer->customize($builder, $params, $queryKey);
            }
        }

        return $builder;
    }
}