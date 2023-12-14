<?php

namespace Ketcau\Security\Voter;

use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\Member;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthorityVoter implements VoterInterface
{
    public function __construct(
        protected RequestStack $requestStack,
        protected KetcauConfig $ketcauConfig
    ){}


    public function vote(TokenInterface $token, mixed $subject, array $attributes)
    {
        $path = null;

        try {
            $request = $this->requestStack->getMainRequest();
        }
        catch (\RuntimeException $ex) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (is_object($request)) {
            $path = rawurlDecode($request->getPathInfo());
        }

        $Member = $token->getUser();
        if ($Member instanceof Member) {
            $adminRoute = $this->ketcauConfig->get('ketcau_admin_route');

        }

        return VoterInterface::ACCESS_GRANTED;
    }
}