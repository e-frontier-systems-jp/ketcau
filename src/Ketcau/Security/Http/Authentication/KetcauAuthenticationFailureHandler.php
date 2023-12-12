<?php

namespace Ketcau\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

class KetcauAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        try {
            $response = parent::onAuthenticationFailure($request, $exception);
        } catch (RouteNotFoundException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
        }

        if ($response instanceof RedirectResponse) {
            if (preg_match('/^https?:\\\\/i', $response->getTargetUrl())) {
                $response->setTargetUrl($request->getUriForPath('/'));
            }
        }

        return $response;
    }
}