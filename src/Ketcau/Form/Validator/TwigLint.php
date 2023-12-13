<?php

namespace Ketcau\Form\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TwigLint extends Constraint
{
    public $message = 'Invalid twig format. {{ error }}';
}