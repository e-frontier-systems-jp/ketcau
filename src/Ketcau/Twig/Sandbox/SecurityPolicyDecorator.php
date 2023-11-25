<?php

namespace Ketcau\Twig\Sandbox;

use Twig\Sandbox\SecurityPolicy as BasePolicy;
use Twig\Sandbox\SecurityPolicyInterface;

class SecurityPolicyDecorator implements SecurityPolicyInterface
{
    private $securityPolicy;


    public function __construct(BasePolicy $securityPolicy)
    {
        $this->securityPolicy = $securityPolicy;
    }

    public function checkSecurity($tags, $filters, $functions): void
    {
        $this->securityPolicy->checkSecurity($tags, $filters, $functions);
    }

    public function checkMethodAllowed($obj, $method): void
    {
        if ($method === '__toString') {
            return;
        }
        $this->securityPolicy->checkMethodAllowed($obj, $method);
    }

    public function checkPropertyAllowed($obj, $property): void
    {
        $this->securityPolicy->checkPropertyAllowed($obj, $property);
    }
}