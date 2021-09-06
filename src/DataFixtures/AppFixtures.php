<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        //Déclarer le faker et importer la class Factory
        $faker = Factory::create('FR-fr');

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


            //On met mes valeurs en dur mais après on chargera dans faker
            $ad->setTitle($title)
                ->setPrice(mt_rand(100,3000))
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setCoverImage($image)
                ->setRooms(mt_rand(1,4))
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
