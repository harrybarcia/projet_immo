<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class AnnonceController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('annonce/accueil.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }
    #[Route('/afficher', name: 'catalogue')]
    public function consulter_annonce(AnnonceRepository $repoannonce, CategorieRepository $repocategorie)
    {
        $annoncesArray = $repoannonce->findAll();
        $categoriesArray = $repocategorie->findAll();
        

        return $this->render('annonce/catalogue.html.twig',[
            "annonces"=>$annoncesArray,
            "categories"=>$categoriesArray,
            
        ]);
        
    }

        /**
     * 
     * @Route("/afficher fiche_annonce/{id<\d+>}", name="fiche_annonce")
     */
    public function fiche_annonce(Annonce $annonceObject, AnnonceRepository $repoannonce, CommentaireRepository $repocommentaire)
                // $id, annonceRepository $repoannonce    
    {
        $mesannonces=($annonceObject->getId());
        return $this->render("annonce/fiche_annonce.html.twig", [
            "annonce"=>$annonceObject,
            "commentaires"=>$repocommentaire->findBy(["annonce"=>$mesannonces]),
        ]);
    }

}
