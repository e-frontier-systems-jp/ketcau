<?php

namespace Ketcau\Twig;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Source;

class Template extends \Twig\Template
{
    public function display(array $context, array $blocks = [])
    {
        $globals = $this->env->getGlobals();

        if (isset($globals['event_dispatcher']) && strpos($this->getTemplateName(), '__string_template__') !== 0) {
            /** @var EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $globals['event_dispatcher'];
            $originCode = $this->env->getLoader()->getSourceContext($this->getTemplateName())->getCode();
            // TODO:
        }
    }

    public function getTemplateName()
    {
    }

    public function getDebugInfo()
    {
        return [];
    }

    public function getSourceContext()
    {
        return new Source('', $this->getTemplateName(), '');
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
    }
}