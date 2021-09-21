<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    //Création de variable 
    private $encoder;
    
    //Initialiser à chaque utilisation de ce methode natif de symfony qui permet d'encoder le mot de pass(il faut une injection de dépendance pour utiliser ce service "UserPasswordHasherInterface" non plus "UserPasswordEncoderInterface" )
    public function __construct(UserPasswordHasherInterface  $encoder){
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        //Déclarer le faker et importer la class Factory
        $faker = Factory::create('FR-fr');

        //Création de tableu vide pour les futures données de l'Objet user() après le setter
        $users = [];

        //On va générer l'URL pour récupérer l'avatar(homme/femme) dans le web de randomuser.me
        //soit: https://randomuser.me/api/portraits/men/21.jpg
        //soit: https://randomuser.me/api/portraits/women/44.jpg
        //On va mettre dans un tableau le genre(homme/femme)
        $genres = ['male','female'];



        //Faire une boucle pour créer plusieurs authors
        for ($i=1; $i <= 10 ; $i++) { 

            //Création des avatars homme/femme avec(randomuser.me) puis on utilise faker pour choisir aléatoire dans le tableau $genre[]
            $genre = $faker->randomElement($genres);

            //Mettre dans une variable l'URL commun
            $avatar = 'https://randomuser.me/api/portraits/';

            //Mettre dans une variable l'ID(1-99) + l'extension avec l'aide de faker
            $avatarId = $faker->numberBetween(1,99).'.jpg';

            //Finaliser l'URL de de l'avatar avec la condition ternaire
            $avatar .= ($genre == 'male' ? 'men/' : 'women/') .$avatarId;


           //Création de nouvel Objet User.php puis l'importer
           $user = new User();

           //Encoder le MDP de l'author + son mot de pass 
           $hash = $this->encoder->hashPassword($user,'password');


           //Avoir quelques phrases avec faker donc avec S
           $content = "<p>".join("<p></p>",$faker->paragraphs(5)). "</p>";

           //On fait le setter de nouvel Objet User()
           $user->setFirstname($faker->firstname)
                ->setLastname($faker->lastname)
                ->setEmail($faker->email)
                ->setAvatar($avatar)
                ->setMdphashed($hash)
                ->setIntroduction($faker->sentence())
                ->setDescription($content)
                //->setSlug()
                ;
           
            //On va persister la variable $user
            $manager->persist($user); 
            //Récupérer dans un tableau tous toutes les données modifiées dans l'Objet user()
            $users[]=$user;

           
        }



        //Faire une boucle pour créer plusieurs annonces
        for($i=1; $i<=30; $i++){

            //Création de nouvel Objet avec la Class Ad.php et importer la class Ad()
            $ad = new Ad();

            //Avoir quelques mots avec faker
            $title = $faker->sentence();

            //Avoir une phrase  avec faker donc sans S
            $introduction = $faker->paragraph(2);

            //Avoir quelques phrases avec faker donc avec S
            $content = "<p>".join("</p><p>",$faker->paragraphs(5)) . "</p>";

            //Avoir quelques images
            $image = $faker->imageUrl(1000,350);

            //Avoir l'author aléatoirement avec le tableau chargé des données apres fait le setter de l'Objet User();
            $user = $users[mt_rand(0,count($users)-1)];


            //Faire le setter de nouvel Objet Ad()
            //On met mes valeurs en dur mais après on chargera dans faker
            $ad->setTitle($title)
                ->setPrice(mt_rand(100,3000))
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setCoverImage($image)
                ->setRooms(mt_rand(1,4))
                ->setAuthor($user)
                ;

            //On persiste les données avec le param $manager
            $manager->persist($ad);


            //Faire une boucle pour créer plusieurs annonces
             for($j=1; $j<=mt_rand(2,5); $j++){

                 //Création de nouvel Objet avec la Class Image.php et importer la class Ad()
                $image = new Image();

                //ici $ad c'est l'identifiant
                $image->setUrl($faker->imageUrl(1000,350))
                      ->setCaption($faker->sentence())
                      ->setAd($ad)
                      ;
                      
                //On persiste les données avec le param $manager
                $manager->persist($image);                      
             }
         }

        //On envoye la requête
        $manager->flush();
    }
}
