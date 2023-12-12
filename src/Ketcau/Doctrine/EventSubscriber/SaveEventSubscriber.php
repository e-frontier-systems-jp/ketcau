<?php

namespace Ketcau\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\Member;
use Ketcau\Request\Context;

class SaveEventSubscriber implements EventSubscriber
{
    protected $requestContext;

    protected $ketcauConfig;


    public function __construct(Context $requestContext, KetcauConfig $ketcauConfig)
    {
        $this->requestContext = $requestContext;
        $this->ketcauConfig = $ketcauConfig;
    }


    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setCreateDate')) {
            $entity->setCreateDate(new \DateTime());
        }
        if (method_exists($entity, 'setUpdateDate')) {
            $entity->setUpdateDate(new \DateTime());
        }

        if (method_exists($entity, 'setCurrencyCode')) {
            $currency = $this->ketcauConfig->get('currency');
            $entity->setCurrencyCode($currency);
        }

        if (method_exists($entity, 'setCreator')) {
            $user = $this->requestContext->getCurrentUser();
            if ($user instanceof Member) {
                $entity->setCreator($user);
            }
        }
    }


    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setUpdateDate')) {
            $entity->setUpdateDate(new \DateTime());
        }

        if (method_exists($entity, 'setCreator')) {
            $user = $this->requestContext->getCurrentUser();
            if ($user instanceof Member) {
                $entity->setCreator($user);
            }
        }
    }
}