<?php

namespace Ketcau\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class TopController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     * @Template("index.twig")
     */
    public function index(): array
    {
        return [];
    }
}