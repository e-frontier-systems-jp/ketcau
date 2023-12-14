<?php

namespace Ketcau\Controller\Admin;

use Ketcau\Controller\AbstractController;
use Ketcau\Form\Type\Admin\LoginType;
use Ketcau\Repository\MemberRepository;
use Ketcau\Repository\PageRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    protected $authorizationChecker;

    protected $helper;

    protected $memberRepository;

    protected $passwordHasher;

//    protected $orderRepository;
//
//    protected $orderStatusRepository;
//
//    protected $customerRepository;
//
//    protected $productRepository;

    private $pageRepository;


    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        AuthenticationUtils $helper,
        MemberRepository $memberRepository,
        UserPasswordHasherInterface $passwordHasher,
        PageRepository $pageRepository
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->helper = $helper;
        $this->memberRepository = $memberRepository;
        $this->passwordHasher = $passwordHasher;

        $this->pageRepository = $pageRepository;
    }


    /**
     * @Route("/%ketcau_admin_route%/login", name="admin_login", methods={"GET", "POST"})
     * @param Request $request
     * @return array|Response
     */
    #[Template("@admin/login.twig")]
    public function login(Request $request)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_homepage');
        }

        $builder = $this->formFactory->createNamedBuilder('', LoginType::class);

        // TODO: Event dispatch

        $form = $builder->getForm();

        return [
            'error' => $this->helper->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }


    /**
     * @Route("/%ketcau_admin_route%/logout", name="admin_logout", methods={"GET"})
     * @param Security $security
     * @return void
     */
    public function logout(Security $security): void
    {
        $security->logout(false);
    }


    /**
     * @Route("/%ketcau_admin_route%/", name="admin_homepage", methods={"GET"})
     */
    #[Template("@admin/index.twig")]
    public function index(Request $request)
    {
        $pages = $this->pageRepository->findAll();

        return [];
    }
}