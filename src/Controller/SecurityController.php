<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
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
     * @Route("/not-the-homepage", name="alternate_page")
     */
    public function alternatePage()
    {
        return $this->render('annonce/annonce_ajouter.html.twig',[]);
    }
}
