<?php

namespace Ketcau\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class ForwardOnly
{
    public function getAliasName()
    {
        return 'forward_only';
    }


    public function allowArray()
    {
        return false;
    }
}