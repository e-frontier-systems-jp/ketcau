<?php

namespace Ketcau\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Common\KetcauConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractController extends Controller
{
    protected $ketcauConfig;

    protected $entityManager;

    protected $translator;

    protected $session;

    protected $formFactory;

    protected $eventDispatcher;


    /**
     * @param KetcauConfig $ketcauConfig
     * @return void
     * @requires
     */
    public function setKetcauConfig(KetcauConfig $ketcauConfig)
    {
        $this->ketcauConfig = $ketcauConfig;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return void
     * @requires
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TranslatorInterface $translator
     * @return void
     * @requires
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param SessionInterface $session
     * @return void
     * @requires
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param FormFactoryInterface $formFactory
     * @return void
     * @requires
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @return void
     * @requires
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}