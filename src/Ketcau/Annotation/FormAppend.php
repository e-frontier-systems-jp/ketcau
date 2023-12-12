<?php

namespace Ketcau\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class FormAppend
{
    public function __construct(
        public bool $auto_render,
        public string $form_theme,
        public string $type,
        public array $options = [],
        public string $style_class = ''
    )
    {}
}