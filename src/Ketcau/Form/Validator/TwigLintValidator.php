<?php

namespace Ketcau\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class TwigLintValidator extends ConstraintValidator
{
    public function __construct(
        protected Environment $twig
    ){}

    public function validate(mixed $value, Constraint $constraint)
    {
        if (is_null($value)) {
            $value = '';
        }

        $realLoader = $this->twig->getLoader();
        try {
            $temporaryLoader = new ArrayLoader(['' => $value]);
            $this->twig->setLoader($temporaryLoader);
            $nodeTree = $this->twig->parse($this->twig->tokenize(new Source($value, '')));
            $this->twig->compile($nodeTree);
        }
        catch (Error $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $e->getMessage())
                ->addViolation();
        }
        $this->twig->setLoader($realLoader);
    }
}