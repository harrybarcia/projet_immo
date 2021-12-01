<?php

namespace App\Controller;

use src\data\SearchData;
use src\data\SearchForm;
use App\Repository\CoordsRepository;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $repoVitrineSection;
    private $requestStack;
    private $request;
    private $manager;


    public function __construct(AnnonceRepository $repoannonce, EntityManagerInterface $manager)
    {
        $this->repoannonce = $repoannonce;

        $this->manager = $manager;
    }


    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('ajouter');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    #[Route('/accueil_user', name: 'accueil_user')]
    public function accueil_user(CoordsRepository $repoCoords, Request $request, AnnonceRepository $repoannonce): Response
    {
    // coordonnées
        $coordsArray = $repoCoords->findAll();
        $annonceArray = $repoannonce->findAll();
    // formulaire filtre
        $data=new SearchData(); // je créé un objet et ses propriétés (q et categorie) et je le stocke dans $data
        $data->page = $request->get('page', 1);
         // je créé mon formulaire qui utilise la classe searchForm que je viens de créé, je précise en second paramètre les données. Comme ça quand je vais faire un handle request ca va modifier cet objet (new search data) qui représente mes données
        $form = $this->createForm(SearchForm::class, $data, [
            'action' => $this->generateUrl('index_user'),
        ]);
        $form->handleRequest($request);
        [$min, $max] = $repoannonce->findMinMax($data);

        $annonces_search=$repoannonce->findSearch($data);   

        return $this->render('security/accueil_user.html.twig', [
            'controller_name_user' => 'AnnonceController',
            "coords_user" => $coordsArray,
            "annonces_user" => $annonceArray,
            "annonces_user" =>$annonces_search,
            "form_user" =>$form->createView(),
            'min_user' => $min,
            'max_user' => $max

        ]);
    }

/**
     * @Route("/index_user", name="index_user")
     */
    public function index_user(AnnonceRepository $repoannonce, Request $request, CoordsRepository $repoCoords): Response
    {
        // pour la partie carto du menu gauche

        
        



        $data=new SearchData(); // je créé un objet et ses propriétés (q et categorie) et je le stocke dans $data
        $data->page = $request->get('page', 1);
        // je créé mon formulaire qui utilise la classe searchForm que je viens de créé, je précise en second paramètre les données. Comme ça quand je vais faire un handle request ca va modifier cet objet (new search data) qui représente mes données

        $form = $this->createForm(SearchForm::class, $data);

        $form->handleRequest($request);
        [$min, $max] = $repoannonce->findMinMax($data);

        $annonces=$repoannonce->findSearch($data);
        dump(gettype($annonces));
        dump($annonces);
        //dd($annonces); renvoit les items qui correspondent à la requête
        $list=$annonces->getItems();
        dump($list);
        
        $coordsi=$repoCoords->findBy(array('annonce' => $list));
        // $filtre = $_GET["categorie"];
        // dump($filtre);
        // $test=$repoannonce->findByCategorie(["categorie"=>$filtre]);
        // if ($test) {
        //     return $this->render('annonce/test.html.twig', ["test"=>$test]);
        // }
        return $this->render('annonce/index_user.html.twig',[
            "annonces_user"=>$annonces,
            "form_user"=>$form->createView(),
            'min' => $min,
            'max' => $max,
            "test" => $coordsi
        ]); 
        
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
        /**
     * @Route("/profil", name="profil")
     */
    public function profil()
    {
        // La méthode getUser() permet de récupérer l'objet user provenant de la table User de l'utilisateur connecté
        
        $user = $this->getUser();
        //dd($user);

        return $this->render('security/profil.html.twig');
    }
    /**
     * @Route("/profil/modification", name="profil_modification")
     */
    public function profil_modification(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        //dump($user);

        //$user->confirmPassword = $user->getPassword();
        //dd($user);

        $form = $this->createForm(UserType::class, $user, ["profil" => true]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "Les données de votre profil ont bien été modifiées");
            return $this->redirectToRoute("profil");

        }


        return $this->render("security/profil_modification.html.twig", [
            "formUser" => $form->createView()
        ]);
    }
    /**
     * @Route("/session/favori", name="ajout_favoris")
     */
    public function like(AnnonceRepository $repoannonce)
    {
        $an=$repoannonce->find(24);
        $test=$this->getUser()->addFavori($an);
        

        $this->manager->persist($test);
        $this->manager->flush();
        dd("");
        return $this->render("annonce/index_user.html.twig");

     
    }


}
