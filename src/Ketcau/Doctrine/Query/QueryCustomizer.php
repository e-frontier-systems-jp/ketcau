<?php

namespace Ketcau\Doctrine\Query;

use Doctrine\ORM\QueryBuilder;

interface QueryCustomizer
{
    public function customize(QueryBuilder $builder, $params, $queryKey);


    public function getQueryKey();
}