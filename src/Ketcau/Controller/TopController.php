<?php

namespace Ketcau\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Routing\Annotation\Route;

class TopController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    #[Template("index.twig")]
    public function index(): array
    {
        return [];
    }
}