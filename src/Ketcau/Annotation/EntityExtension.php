<?php

namespace Ketcau\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityExtension
{
    public function __construct(
        public string $value
    )
    {}
}