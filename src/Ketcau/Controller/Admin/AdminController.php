<?php

namespace Ketcau\Controller\Admin;

use Ketcau\Controller\AbstractController;
use Ketcau\Repository\MemberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
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


    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        AuthenticationUtils $helper,
        MemberRepository $memberRepository,
        UserPasswordHasherInterface $passwordHasher,
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->helper = $helper;
        $this->memberRepository = $memberRepository;
        $this->passwordHasher = $passwordHasher;

    }


    /**
     * @Route("/%ketcau_admin_route%/", name="admin_homepage", methods={"GET"})
     * @Template("@admin/index.twig")
     */
    public function index(Request $request)
    {
        return [];
    }
}