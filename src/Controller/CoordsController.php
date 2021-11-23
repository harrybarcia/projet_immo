<?php

namespace App\Controller;

use App\Repository\CoordsRepository;
use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoordsController extends AbstractController
{

    /**
     * @Route("/coords_afficher", name="coords_afficher")
     */
    public function coords_afficher(CoordsRepository $repoCoords, AnnonceRepository $repoAnnonce )
    {

        $coordsArray = $repoCoords->findAll();
        $annonceArray = $repoAnnonce->findAll();
        dump($coordsArray);
        dd($annonceArray);
        return $this->render("coords/coords_afficher.html.twig", [
            "coords" => $coordsArray,
            "annonces" => $annonceArray,



        ]);
    }
}
