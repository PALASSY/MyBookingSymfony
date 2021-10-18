<?php

//namespace est le chemin où l'on se trouve actuellement 
namespace App\Controller;

//Importation des class (héritage/Annotation/Response)

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


//Création de page: 1-une fonction publique 1-une route 1-reponse/action
class HomeController extends AbstractController
{

    // l'annotation permet d'aller vers la page twig(home)
    /**
     * Création de 1er Route (page première)
     * @Route("/", name ="homepage")
     */          
    public function home(AdRepository $adRepo, UserRepository $userRepo)
    {

        //1er param c'est le fichier twig
        //2em param c'est un tableau pour récupérer les fichiers(JSON,XML,noSql)
        return $this->render('home.html.twig',
                            ['ads'=>$adRepo->findBestAds(3),
                             'users' => $userRepo->findBestUsers(2)]);
    }



    //Annotation des chemins  @return void = retourne un vide
    /**
     * Création de la Route qui salut l'utilisateur
     * @Route("/profil", name = "hello-pardefaut")
     * @Route("/hello/{nom}", name = "hello-nom")
     * @Route("/hello/{nom}/access/{access}", name = "hello-nometaccess")
     * @return void
     */

    //L'annotation permet d'aller vers la page twig(hello)
    //Avec les params par défauts
    public function hello($nom="toi qui se veut être anonyme",$access="curieux")
    {
        //1er param c'est le fichier twig
        //2em param c'est un tableau pour récupérer les fichiers(JSON,XML,noSql)
        return $this->render('hello.html.twig', ['titre'=>'Page de profil de twig', 'nom'=> $nom, 'access'=>$access]);
    }
}
