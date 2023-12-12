<?php

namespace Ketcau\Request;

use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\Member;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Context
{
    protected $requestStack;

    protected $ketcauConfig;

    private $tokenStorage;


    public function __construct(RequestStack $requestStack, KetcauConfig $ketcauConfig, TokenStorageInterface $tokenStorage)
    {
        $this->requestStack = $requestStack;
        $this->ketcauConfig = $ketcauConfig;
        $this->tokenStorage = $tokenStorage;
    }


    public function isAdmin(): bool
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            return false;
        }

        $pathInfo = \rawurldecode($request->getPathInfo());
        $adminPath = $this->ketcauConfig->get('ketcau_admin_route');
        $adminPath = '/'. \trim($adminPath, '/'). '/';

        return str_starts_with($pathInfo, $adminPath);
    }


    public function isFront(): bool
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            return false;
        }

        return false === $this->isAdmin();
    }


    /**
     * @return UserInterface
     */
    public function getCurrentUser()
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            return null;
        }

        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}