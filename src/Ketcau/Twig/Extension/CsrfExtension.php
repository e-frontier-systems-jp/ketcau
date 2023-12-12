<?php

namespace Ketcau\Twig\Extension;

use Ketcau\Common\Constant;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    protected $tokenManager;


    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }


    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_token_for_anchor', [$this, 'getCsrfTokenForAnchor'], ['is_safe' => ['all']]),
        ];
    }


    public function getCsrfTokenForAnchor()
    {
        $token = $this->tokenManager->getToken(Constant::TOKEN_NAME)->getValue();

        return 'token-for-anchor=\''. $token. '\'';
    }


    public function getCsrfToken()
    {
        return $this->tokenManager->getToken(Constant::TOKEN_NAME)->getValue();
    }
}