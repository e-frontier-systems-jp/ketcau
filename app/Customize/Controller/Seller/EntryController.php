<?php

namespace Customize\Controller\Seller;

use Customize\Entity\Seller;
use Ketcau\Controller\AbstractController;
use Ketcau\Form\Type\Front\SellerEntryType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EntryController extends AbstractController
{
    /**
     * @Route("/seller/entry", name="seller_entry", methods={"GET", "POST"})
     */
    #[Template("Seller/Entry/index.twig")]
    public function index(Request $request)
    {
        if ($this->isGranted('ROLE_SELLER')) {
            // TODO: 認証済
        }

        $Seller = new Seller();

        $builder = $this->formFactory->createBuilder(SellerEntryType::class, $Seller);

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
        ];
    }
}