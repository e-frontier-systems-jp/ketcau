<?php

namespace Ketcau\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Common\Constant;
use Ketcau\Common\KetcauConfig;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractController extends Controller
{
    /**
     * @var KetcauConfig
     */
    protected $ketcauConfig;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;


    /**
     * @param KetcauConfig $ketcauConfig
     * @return void
     * @required
     */
    public function setKetcauConfig(KetcauConfig $ketcauConfig)
    {
        $this->ketcauConfig = $ketcauConfig;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return void
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TranslatorInterface $translator
     * @return void
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param RequestStack $requestStack
     * @return void
     * @required
     */
    public function setSession(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    /**
     * @param FormFactoryInterface $formFactory
     * @return void
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @return void
     * @required
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }



    public function addSuccess($message, $namespace = 'front')
    {
        $this->addFlash('ketcau.'. $namespace. '.success', $message);
    }

    public function addSuccessOnce($message, $namespace = 'front')
    {
        $this->addFlashOnce('ketcau.'. $namespace. '.success', $message);
    }

    public function addError($message, $namespace = 'front')
    {
        $this->addFlash('ketcau.'. $namespace. '.error', $message);
    }

    public function addErrorOnce($message, $namespace = 'front')
    {
        $this->addFlashOnce('ketcau.', $namespace. '.error', $message);
    }

    public function addDanger($message, $namespace = 'front')
    {
        $this->addFlash('ketcau.'. $namespace. '.danger', $message);
    }

    public function addDangerOnce($message, $namespace = 'front')
    {
        $this->addFlashOnce('ketcau.', $namespace. '.danger', $message);
    }

    public function addWarning($message, $namespace = 'front')
    {
        $this->addFlash('ketcau.', $namespace. '.warning', $message);
    }

    public function addWarningOnce($message, $namespace = 'front')
    {
        $this->addFlashOnce('ketcau.'. $namespace. '.warning', $message);
    }

    public function addInfo($message, $namespace = 'front')
    {
        $this->addFlash('ketcau.'. $namespace. '.info', $message);
    }

    public function addInfoOnce($message, $namespace = 'front')
    {
        $this->addFlashOnce('ketcau.'. $namespace. '.info', $message);
    }

    public function addRequestError($message, $namespace = 'front')
    {
        $this->addFlash('ketcau.'. $namespace. '.request.error', $message);
    }

    public function addRequestErrorOnce($message, $namespace = 'front')
    {
        $this->addFlashOnce('ketcau.'. $namespace. '.request.error', $message);
    }

    public function clearMessage()
    {
        $this->session->getFlashBag()->clear();
    }

    public function deleteMessage()
    {
        $this->clearMessage();
        $this->addWarning('admin.common.delete_error_already_deleted', 'admin');
    }


    protected function addFlash(string $type, mixed $message): void
    {
        try {
            parent::addFlash($type, $message);
        } catch (\LogicException $e) {
            $this->session->getFlashBag()->add($type, $message);
        }
    }

    protected function addFlashOnce(string $type, $message): void
    {
        if (!$this->hasMessage($type)) {
            $this->addFlash($type, $message);
        }
    }

    protected function hasMessage(string $type): bool
    {
        return $this->session->getFlashBag()->has($type);
    }


    public function isTokenValid(): bool
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $token = $request->get(Constant::TOKEN_NAME)
            ? $request->get(Constant::TOKEN_NAME)
            : $request->headers->get('KETCAU_CSRF_TOKEN');

        if (!$this->isCsrfTokenValid(Constant::TOKEN_NAME, $token)) {
            throw new AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }
}