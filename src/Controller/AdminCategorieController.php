<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/gestion_categorie")
 */


class AdminCategorieController extends AbstractController
{
    /*
        /gestion_categorie/afficher           name="categorie_afficher"       => categorie_afficher.html.twig
        /gestion_categorie/ajouter            name="categorie_ajouter"        => categorie_ajouter.html.twig
        /gestion_categorie/modifier/{id}      name="categorie_modifier"        => categorie_modifier.html.twig
        /gestion_categorie/supprimer/{id}     name="categorie_supprimer"
        
    */


    /**
     * @Route("/afficher", name="categorie_afficher")
     */
    public function categorie_afficher(CategorieRepository $repoCategorie)
    {

        $categorieArray = $repoCategorie->findAll();
        //dd($categorieArray);
        return $this->render("admin_categorie/categorie_afficher.html.twig", [
            "categories" => $categorieArray
        ]);
    }



    /**
     * @Route("/ajouter", name="categorie_ajouter")
     * @Route("/modifier/{id}", name="categorie_modifier")
     */
    public function categorie_ajouter_modifier(Categorie $categorie = null, Request $request, EntityManagerInterface $manager)
    {

        if(!$categorie)
        {
            $categorie = new Categorie;
        }

        //dd($categorie);
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $modif = $categorie->getId() !== null;

            $manager->persist($categorie);
            $manager->flush();

            $this->addFlash("success", ($modif) ? "La catégorie N°" . $categorie->getId() . " a bien été modifiée" : "La catégorie N°" . $categorie->getId() . " a bien été ajoutée" );
            
            return $this->redirectToRoute('categorie_afficher');
        }

        return $this->render("admin_categorie/categorie_ajouter_modifier.html.twig", [
            "formCategorie" => $form->createView(),
            "categorie" => $categorie,
            "modification" => $categorie->getId() !== null
        ]);
    }



    
    /**
     * @Route("/supprimer/{id}", name="categorie_supprimer")
     */
    public function categorie_supprimer(Categorie $categorie, EntityManagerInterface $manager){
    
    
        $manager->remove($categorie);
        $manager->flush();
    
        $this->addFlash("success","la categorie a bien été supprimée"); 
    
    
        return $this->redirectToRoute("categorie_afficher");
    }



}



