<?php

namespace Ketcau\Security\User;

use Ketcau\Entity\Master\Work;
use Ketcau\Entity\Member;
use Ketcau\Repository\MemberRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MemberProvider implements UserProviderInterface
{
    public function __construct(
        protected MemberRepository $memberRepository
    ){}


    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $Member = $this->memberRepository->findOneBy(['login_id' => $identifier, 'Work' => Work::ACTIVE]);

        if (null === $Member) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exists', $identifier));
        }

        return $Member;
    }


    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Member) {
            throw new UnsupportedUserException(sprintf('Instance of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return Member::class === $class || is_subclass_of($class, Member::class);
    }
}