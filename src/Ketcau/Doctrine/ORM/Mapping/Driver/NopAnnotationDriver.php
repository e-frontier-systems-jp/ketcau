<?php

namespace Ketcau\Doctrine\ORM\Mapping\Driver;

class NopAnnotationDriver extends \Doctrine\ORM\Mapping\Driver\AnnotationDriver
{
    public function getAllClassNames(): array
    {
        return [];
    }
}