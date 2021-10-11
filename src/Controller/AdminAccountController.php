<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * Page de connexion
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $email =$utils->getLastUsername();
        return $this->render('admin/account/login.html.twig', [
            'hasError'=>$error!==null,
            'email'=>$email
        ]);
    }


    /**
     * Page de déconnexion
     * 
     * @Route("/admin/logout", name="admin_account_logout")
     * @return void
     */
    public function logout()
    {
        //C'est config/packages/security.yaml qui gère la deconnexion
    }

}
