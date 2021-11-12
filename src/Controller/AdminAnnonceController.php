<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin/gestion_annonce")
 */

class AdminAnnonceController extends AbstractController
{
    /**
     * @Route("/afficher", name="annonce_afficher")
     */
        public function annonce_afficher(AnnonceRepository $repoAnnonce)
        {
            $annoncesArray = $repoAnnonce->findAll();

            //dd($annoncesArray);// c'est un tableau d'objets
            


            return $this->render('admin_annonce/annonce_afficher.html.twig', [
                "annonces" => $annoncesArray
            ]);
        }


    /**
     * @Route("/ajouter", name="annonce_ajouter")
     */
        public function ajouter_annonce(Request $request, EntityManagerInterface $manager)
        {

            if($this->isGranted('IS_ANONYMOUS')) //si la personne connectée est anonyme
            { 
                $this->addFlash(
                'success',
                'Veuillez vous connecter pour pouvoir déposer une annonce'
                );
                    return $this->redirectToRoute("login");
            }
            // ----------Je créé un nouvel objet annonce------------
            $annonce=new Annonce;
            //dd($annonce);
            $form = $this->createForm(AnnonceType::class, $annonce);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $imageFile = $form->get('image')->getData();
                if($imageFile) // si $imageFile n'est pas vide/null ==> une image a été upload
                {

                    $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();
                    
                    $imageFile->move(
                        $this->getParameter("images_annonces"),
                        $nomImage
                    );
                    $annonce->setImage($nomImage);
                }
                $annonce->setDateEnregistrement(new \DateTimeImmutable('now'));
                $user=$this->getUser();
                $annonce->setUser($user);


                $manager->persist($annonce);
                $manager->flush();
                
                
                $this->addFlash("success", "L'annonce N°" . $annonce->getId() . " a bien été déposée");
                return $this->redirectToRoute("accueil");

            }

            return $this->render('admin_annonce/annonce_ajouter.html.twig',[
                "formAnnonce"=>$form->createView()
            ]);
    }

    /**

     * @Route("/modifier/{id<\d+>}", name="annonce_modifier")
     */
    public function annonce_modifier(Annonce $annonce, Request $request, EntityManagerInterface $manager) // objet de la class Annonce
    {
        



        $form = $this->createForm(AnnonceType::class, $annonce);
        dump($form->createView());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
        $manager->persist($annonce); //avec persist on peut ajouter ou modifier un annonce. Si l'id est null, il va créer l'annonce si l'id
        // existe, il va l'update.
        $manager->flush(); 

        $this->addFlash("success", "Le annonce N°" . $annonce->getId() . " a bien été modifié");

        return $this->redirectToRoute("annonce_afficher");
 
        }

        return $this->render('admin_annonce/annonce_modifier.html.twig', [
            "annonce" => $annonce, /* ce 2eme argument est utile si on veut afficher des données de la variable dans le twig */
        "formAnnonce"=>$form->createView()]);
    }


 
    /**
     * @Route("/supprimer/{id}", name="annonce_supprimer") 
     * 
     * 
     */
    public function annonce_supprimer(Annonce $annonce, EntityManagerInterface $manager ){

        if($annonce->getImage()){
            dump(gettype($annonce));
            dump($annonce->getImage());
            unlink($this->getParameter("images_annonces") . '/' . $annonce->getImage()); //si mon produit contient une image alors je la supprr
            
        }
        
            $idAnnonce=$annonce->getId();
            
            $manager->remove($annonce);
            $manager->flush ();

            $this->addFlash("success","l'annonce $idAnnonce bien été supprimée"); 
        

        return $this->redirectToRoute("annonce_afficher");


    }
}
