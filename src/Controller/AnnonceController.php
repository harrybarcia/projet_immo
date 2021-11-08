<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * 
 * @Route("/admin") 
 */


class AnnonceController extends AbstractController
{
    #[Route('/', name: 'annonce')]
    public function index(): Response
    {
        return $this->render('annonce/accueil.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }
}
