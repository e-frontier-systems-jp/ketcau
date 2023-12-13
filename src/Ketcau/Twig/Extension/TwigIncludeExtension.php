<?php

namespace Ketcau\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigIncludeExtension extends AbstractExtension
{
    public function __construct(
        protected Environment $twig
    ){}


    public function getFunctions(): array
    {
        return [
            new TwigFunction('include_dispatch', [$this, 'include_dispatch'],
                ['needs_context' => true, 'is_safe' => ['all']]),
        ];
    }


    public function include_dispatch($context, $template, $variables = []): string
    {
        if (!empty($variables)) {
            $context = array_merge($context, $variables);
        }
        return $this->twig->render($template, $context);
    }
}