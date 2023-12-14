<?php

namespace Ketcau\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TruncateHyphenListener implements EventSubscriberInterface
{
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $data = str_replace('-', '', $data);
        $event->setData($data);
    }


    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }
}