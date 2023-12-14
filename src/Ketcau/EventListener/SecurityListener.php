<?php

namespace Ketcau\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Entity\Member;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecurityListener implements EventSubscriberInterface
{
    public function __construct(
        protected EntityManagerInterface $em
    ){}


    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $User = $event->getAuthenticationToken()->getUser();

        if ($User instanceof Member) {
            $User->setLoginDate(new \DateTime());
            $this->em->persist($User);
            $this->em->flush();
        }
    }


    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'
        ];
    }
}