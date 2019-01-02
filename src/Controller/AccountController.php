<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\UpdatePassword;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * 
     * Permet de se connecter
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

     /**
     * 
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout()
    {
    }

    /**
     * Gestion de l'inscription
     * @Route("/register", name="account_register")
     * @return Response
     * 
     */
     public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
         
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getHash());

            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a été créé avec succés !"
            );

             return $this-> redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);

     }
     /**
      * Permet d'éditer et de modifier le profil d'un utilisateur
      *@Route("account/profile", name="account_profile") 
      *@IsGranted("ROLE_USER")
      * @return Response
      */
     public function profile(Request $request, ObjectManager $manager){
        //On récupére le user actuellement connecté
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Modifications enregistrées avec succés !"
            );

        }

         return $this->render('account\profile.html.twig', [
             'form' => $form->createView()
         ]);
     }

     /**
      * Modification du mot de passe
      *@Route("/account/update-password", name="account_password")
      *@IsGranted("ROLE_USER")
      * @return Response
      */
     public function updatepassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){

        $user = $this->getUser();

        $passwordUpdate = new UpdatePassword();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // 1 - Vérifier que le champ oldPassword du formulaire soit le même que celui en base de données avec la fonction password_verify
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
                // on gére l'erreur avec l'API form_api
                $form->get('oldPassword')->addError(new FormError("Le mot de passe saisi ne correspond pas à votre mot de psse actuel !"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Modification du mot de passe avec succés !"
                );

                return $this->redirectToRoute('homepage');

            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
     
    }

    /**
     * Permet d'afficher le profil d'un utilsateur si il est connecté
     * 
     *@Route("/account", name="account_index") 
     *@IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * Permet de visualiser l'ensemble des réserevations faites par l'utilisateur
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return Response
     */
    public function bookings(){


        return $this->render('account/bookings.html.twig');
    }
}
