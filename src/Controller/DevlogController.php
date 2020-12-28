<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevlogController extends AbstractController
{
    /**
     * @Route("/devlog", name="devlog")
     */
    public function index(): Response
    {
        return $this->render('devlog/index.html.twig', [
            'controller_name' => 'DevlogController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('devlog/home.html.twig');
    }
}
