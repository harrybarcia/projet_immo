<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Annonce;
use src\data\SearchData;
use src\data\SearchForm;
use App\Form\AnnonceType;
use App\Repository\PhotoRepository;
use App\Repository\CoordsRepository;
use App\Repository\AnnonceRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class AnnonceController extends AbstractController

{


    #[Route('/', name: 'accueil')]
    public function accueil(CoordsRepository $repoCoords, Request $request, AnnonceRepository $repoannonce): Response
    {
    // coordonnées
        $coordsArray = $repoCoords->findAll();
        $annonceArray = $repoannonce->findAll();
    // formulaire filtre
        $data=new SearchData(); // je créé un objet et ses propriétés (q et categorie) et je le stocke dans $data
        $data->page = $request->get('page', 1);
         // je créé mon formulaire qui utilise la classe searchForm que je viens de créé, je précise en second paramètre les données. Comme ça quand je vais faire un handle request ca va modifier cet objet (new search data) qui représente mes données
        $form = $this->createForm(SearchForm::class, $data, [
            'action' => $this->generateUrl('index'),
        ]);
        $form->handleRequest($request);
        [$min, $max] = $repoannonce->findMinMax($data);

        $annonces_search=$repoannonce->findSearch($data);   

        return $this->render('annonce/accueil.html.twig', [
            'controller_name' => 'AnnonceController',
            "coords" => $coordsArray,
            "annonces" => $annonceArray,
            "annonces"=>$annonces_search,
            "form"=>$form->createView(),
            'min' => $min,
            'max' => $max

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
     * @Route("/afficher/fiche_annonce/{id<\d+>}", name="fiche_annonce")
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

       /**
     * @Route("/ajouter", name="annonce_ajouter")
     */
    public function annonce_ajouter(Request $request, EntityManagerInterface $manager, SessionInterface $session)
    {
        $test=$request->getSession();
        dump($test);
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
        $form = $this->createForm(AnnonceType::class, $annonce, array("ajouter"=>true));
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $annonce->setDateEnregistrement(new \DateTimeImmutable('now'));
            
            $user=$this->getUser();
            $annonce->setUser($user);

            $manager->persist($annonce);
            $manager->flush();

            $photoFile = $form->get('photo')->getData();

            if($photoFile)
            {
                //-- le champs photo est un tableau de mon entité annonce--
                for($c = 0; $c < count($photoFile); $c++)
                {
                // --- pour chaque tour de boucle, je génère un nom-----
                $nomImage = md5(uniqid()).'.'.$photoFile[$c]->guessExtension();
                
                // --Je copie le fichier dans le dossier uploads--
                $photoFile[$c]->move(
                    $this->getParameter('images_annonces'),$nomImage);
                // -- je créé un objet et je l'insère dans ma bdd
                $image = new Photo();
                $image->setNom($nomImage);
                $image->setAnnonce($annonce);// dans le champs annonce de mon objet image
                // il sait qu'il doit insérer la clef primaire
                $manager->persist($image); // on persiste l'instance
                $manager->flush(); // on envoie l'instance en BDD
            
                }
            }  
            $this->addFlash("success", "Votre annonce a bien été déposée");
            return $this->redirectToRoute("accueil");
        }
        return $this->render('annonce/annonce_ajouter.html.twig',[
            
            "formAnnonce"=>$form->createView()
        ]);
    }
    /**
     * @Route("/modifier/{id<\d+>}", name="annonce_modifier")
     */
    public function annonce_modifier(Annonce $annonce, Request $request, EntityManagerInterface $manager, PhotoRepository $repophotos) // objet de la class Annonce
    {

        $form = $this->createForm(AnnonceType::class, $annonce, array("modifier"=>true));
        dump($form->createView());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if($annonce->getPhotos()) // si get photos existe
                {
                    $photos=$repophotos->findBy(["annonce"=>$annonce->getId()]);
                    for($d = 0; $d < count($photos); $d++){
                        ($photos[$d]->getNom());
                        unlink($this->getParameter("images_annonces") . '/' . $photos[$d]->getNom()); 
                        $manager->remove($photos[$d]);
                        $manager->flush ();
                        
                    }
                }
            $photoFile=$form->get('photoFile')->getData();
            
            if($photoFile) // si une photo a été upload
            {
                for($c = 0; $c < count($photoFile); $c++)
                 
                {
                    
                    dump("val de c:");dump($c);
                    dump($photoFile)[$c];
                    

                    $nomImage = md5(uniqid()).'.'.$photoFile[$c]->guessExtension(); // a chaque photo, j'attribue un onm
                    dump("3");
                    dump($nomImage);
                    $photoFile[$c]->move($this->getParameter("images_annonces"),$nomImage); // déplace dan upload
                    // -- je créé un objet et je l'insère dans ma bdd
                    $image = new Photo();
                    $image->setNom($nomImage);
                    $image->setAnnonce($annonce);// dans le champs annonce de mon objet image
                    // il sait qu'il doit insérer la clef primaire
                    $manager->persist($image); // on persiste l'instance
                    $manager->flush(); // on envoie l'instance en BDD
                }
            }
           
            $manager->persist($annonce); //avec persist on peut ajouter ou modifier un annonce. Si l'id est null, il va créer l'annonce si l'id
            // existe, il va l'update.
            $manager->flush(); 

            $this->addFlash("success", "L'annonce N°" . $annonce->getId() . " a bien été modifié");

            return $this->redirectToRoute("mes_annonces");
    
        }

        return $this->render('annonce/annonce_modifier.html.twig', [
            "annonce" => $annonce, /* ce 2eme argument est utile si on veut afficher des données de la variable dans le twig */
        "formAnnonce_modif"=>$form->createView()]);
    }
    /**
     * @Route("/supprimer/{id}", name="annonce_supprimer") 
     */
    public function annonce_supprimer(Annonce $annonce, EntityManagerInterface $manager, PhotoRepository $repophotos, CommentaireRepository $repocommentaire ){
        
        $photos=$repophotos->findBy(["annonce"=>$annonce->getId()]);
        // dump($photos[0]->getNom());
        dump($annonce);
            if ($photos){
                for ($i=0; $i < count($photos) ; $i++) { 
                    unlink($this->getParameter("images_annonces") . '/' . $photos[$i]->getNom()); 
                    $manager->remove($photos[$i]);        
                    }
                }
        $commentaire=$repocommentaire->findBy(["annonce"=>$annonce->getId()]);        
            if ($commentaire){
                for ($i=0; $i < count($commentaire) ; $i++) { 
                $manager->remove($commentaire[$i]);        
                    }
                }
            $idAnnonce=$annonce->getId();
            
            $manager->remove($annonce);
            $manager->flush ();

            $this->addFlash("success","l'annonce $idAnnonce a bien été supprimée"); 
        

        return $this->redirectToRoute("mes_annonces");


    }
    #[Route('/annonce/mes_annonces', name: 'mes_annonces')]
    public function mesannonces(AnnonceRepository $repoannonce)
    {
        return $this->render('annonce/mes_annonces.html.twig',[
            "annonces"=>$repoannonce->findBy(['user'=>$this->getUser()->getId()]),
        ]);
        
    }

    /**
     * @Route("/index", name="index")
     */
    public function index(AnnonceRepository $repoannonce, Request $request, CoordsRepository $repoCoords): Response
    {
        // pour la partie carto du menu gauche
        $coordsArray = $repoCoords->findAll();

        $data=new SearchData(); // je créé un objet et ses propriétés (q et categorie) et je le stocke dans $data
        $data->page = $request->get('page', 1);
        // je créé mon formulaire qui utilise la classe searchForm que je viens de créé, je précise en second paramètre les données. Comme ça quand je vais faire un handle request ca va modifier cet objet (new search data) qui représente mes données

        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);
        [$min, $max] = $repoannonce->findMinMax($data);

        $annonces=$repoannonce->findSearch($data);
        // $filtre = $_GET["categorie"];
        // dump($filtre);
        // $test=$repoannonce->findByCategorie(["categorie"=>$filtre]);
        // if ($test) {
        //     return $this->render('annonce/test.html.twig', ["test"=>$test]);
        // }
        return $this->render('annonce/index.html.twig',[
            "annonces"=>$annonces,
            "form"=>$form->createView(),
            'min' => $min,
            'max' => $max,
            "coords" => $coordsArray,
        ]); 
        
    }
        /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        
        return $this->render('carto.html.twig', [
            
        ]);
    }
}
