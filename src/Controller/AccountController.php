<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Page de connexion
     * 
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $email = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'error'=>$error!==null,
            'email'=>$email
        ]);
    }


    /**
     * Page de déconnexion
     * 
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout(){
        //C'est config/packages/security.yaml qui gère la deconnexion
    }


    /**
     * Page d'inscription
     * 
     * @Route("/register", name="account_register")
     * @return Response
     */
    public function register(Request $request, UserPasswordHasherInterface $encoder, ObjectManager $manager){
        //On va récupérer les champs de l'Entity User.php
        $user = new User();
        //Création de formulaire à partir de l'Entity User
        $form = $this->createForm(RegistrationType::class,$user);
        //Récupérer les données par POST
        $form->handleRequest($request);
        //Sécurité: Soumission et validation
        if($form->isSubmitted() && $form->isValid()){
            //On va récupérer le MDP en  utilisant la methode native de Symfony (UserPasswordEncoderInterface) qui hache le MDP / on fait une injection de dépendance
            $mdphashed = $encoder->hashPassword($user,$user->getMdphashed());
            //Modifier le MDP avec le setter de l'Entity User
            $user->setMdphashed($mdphashed);
            //On va sauvegarder les données de la requête par (ObjectManager)
            $manager->persist($user);
            //On va envoyer les données dans BD 
            $manager->flush();
            //Créer un message flush
            $this->addFlash('success', "<strong>{$user->getLastname()}</strong>, votre annonce est créer avec succès !");
            //Faire une redirection sur la page de connexion si l'inscription est passé avec succès
            return $this->redirectToRoute('account_login');
        }  
        return $this->render('account/register.html.twig', ['form'=>$form->createView()]);
    }


    /**
     * Page de modification du profil de l'utilisateur connecté
     * 
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request,ObjectManager $manager){
        //Récupérer l'utilisateur connecté
        $user = $this->getUser();
        //Création de formulaire à partir de AccountType
        $form = $this->createForm(AccountType::class, $user);
        //Injecter les données de l'user dans ce formulaire 
        $form->handleRequest($request);
        //La sécurité: de validation et de soumission
        if($form->isSubmitted() && $form->isValid()){
            //Sauvegarder les données
            $manager->persist($user);
            //Envoyer les données de la requête dans BD
            $manager->flush();
            //Envoyer un message flash
            $this->addflash("success","<strong>{$user->getLastname()}</strong>, Votre profil a bien été modifié!!!");
        }
        return $this->render("account/profile.html.twig", ['form'=>$form->createView()]);
    }


    /**
     * Page de modification du mot de passe de l'utilisateur connecté
     * 
     * @Route("/account/password-update", name="account_passwordupdate")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updPassword(Request $request,UserPasswordHasherInterface $encoder,ObjectManager $manager)
    {
        //On va récupérer les champs de l'Entity PasswordUpdate.php
        $passwordUpdate = new PasswordUpdate();
        //On va récupérer l'utilisateur connecté 
        $user = $this->getUser();
        //Création de formulaire à partir de PasswordUpdateType 
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        //Injecter les données de l'user dans ce formulaire 
        $form->handleRequest($request);

        //On assure la sécurité de soumission et validation 
        if($form->isSubmitted() && $form->isValid()){
            //Vérifié si le mot de passe actuel est le bon 
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getMdphashed())){
                //erreur, mot de passe ne sont pas identique
                //$this->addFlash('warning',"Votre mot de passe actuel n'est pas le bon");
                $form->get('oldPassword')->addError(new FormError("Votre mot de passe actuel n'est le votre"));
            }else{
                //On va récupérer le nouveau mot de passe
                $newPassword =  $passwordUpdate->getNewPassword();
                //On va hacher ce nouveau MDP
                $mdphashed = $encoder->hashPassword($user,$newPassword);
                //On va setter le MDP de l'user
                $user->setMdphashed($mdphashed);
                //Sauvegarder le mot de passe
                $manager->persist($user);
                //On va sauvegarder les données dans la BD
                $manager->flush();
                //On va afficher un message flash
                $this->addFlash("succes","<strong>{$user->getLastname()}</strong>, votre mot de passe a bien été enregistré");
                //On va faire une rédirection 
                return $this->redirectToRoute("account_profile");
            }
        }
        //L'action
        return $this->render("account/password.html.twig", ['form' => $form->createView()]);
    }


    /**
     * Page affichage du compte de l'utilisateur connecté 
     * @Route("/account", name="account_user")
     * 
     * @return Response
     */
    public function myAccount(){
        //Retourne l'utilisateur connecté avec la methode getUser() de php
        return $this->render('user/index.html.twig',['user'=>$this->getUser(),'controller_name'=>"Affichage du compte de l'utilisateur connecté"]);
    }


    

}
