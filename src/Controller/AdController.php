<?php

namespace App\Controller;

//La Class Ad doit être utilisé pourque la Class AdRepository le reflète ainsi on peut récupérer tous les champs 
use App\Entity\Ad;
use App\Entity\Image;
//utilisé pour l'injection de dépendance
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Permet d'afficher toutes annonces
     * @Route("/ads", name="ads_list")
     */

    //Ici AdRepository reflète la Class Ad et peut tout récupérer les champs
    //c'est type le hiting
    public function index(AdRepository $repo)
    {

        //Récupérer dans cette Class de Repository tous les champs(propriety: titre,slug,price,image...)
        $ads = $repo->findAll();

        //Rajouter dans le tableau ces champs récupérés
        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une annonce à partir d'un formulaire
     * @Route("ads/new",name="ads_create");
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager)
    {
        //Instancier un nouvel Objet Ad(à importer)
        $ad = new Ad();

        //Instancier un nouvel Objet Image(et à importer) ceci était utilisé pour servir d'exemple
        //$images = new Image();
        //Modifier l'image(url/caption)
        //$images->setUrl('https://place-hold.it/600x300')
            //->setCaption('Titre image 1');
        //Ajouter l'image dans nouvel Objet Ad()
        //$ad->addYe($images);


        //Lancer la fabrication de formulaire avec FORMBUILDER (c'est un gros Objet qui contient enorment des méthodes)
        //$form = $this->createFormBuilder($ad)
        // et configurer avec la methode add de formbuilder pour les champs qu'on a besoin
        // ->add('title')
        //->add('introduction')
        //->add('content')
        //->add('rooms')
        //->add('price')
        //->add('coverImage')
        //->add('save', SubmitType::class, ['label' => 'Créer', 'attr' => ['class' => 'btn btn-warning btn-large']])
        //créer le formulaire 
        // ->getForm();

        //Instanciation de la nouvelle Class form de formulaire(src/Form/AnnonceType.php)
        $form = $this->createForm(AnnonceType::class, $ad);

        //Récupération des données qui on été récupérées par $POST
        $form->handleRequest($request);

        //Vérifier les données de nouvel Objet Ad()
        //dump($ad);
        //die;

        //Toujours vérifier la soumission et validation d'un formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Faire une boucle pour récuper toutes les images récupérées dans le formulaire de collection append
            foreach ($ad->getYes() as $image) {
                # modifier l'image dans l'Entity (Ad.php) càd rélié à l'annonce
                $image->setAd($ad);

                #demande à Doctrine de sauvegarder ces données dans la BD dans l'Objet $manager(Injection de dépendance)
                $manager->persist($image);
            }

             //Relié l'author qui est connecté au formulaire (création d'une annonce) pour que les données seront enregistrées à l'author qui est connecté       
             $ad->setAuthor($this->getUser());

            //Si soumission ok et validation ok, on demande à Doctrine de sauvegarder ces données dans la BD dans l'Objet $manager(Injection de dépendance)
            $manager->persist($ad);

            //Envoyer les données(la requête) comme dans fixtures
            $manager->flush();

            //Envoye un message flash
            $this->addFlash('success', "Annonce <strong>{$ad->getTitle()}</strong> est créer avec succès !");

            //Rediriger pour afficher une annonce par rapport à son ADN(slug)
            return $this->redirectToRoute('ads_single', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig', ['form' => $form->createView()]);
    }



    /**
     * L'URL qui permet d'afficher une annonce par rapport à son ADN slug 
     * @Route("/ads/{slug}", name="ads_single")
     *  
     * Retourne une réponse
     * @return Response
     */

    //1er param c'est l'ADN(champ)
    //2èm param c'est la Class interface (type hiting)qui a été supprimé et remplacé par la Class src/Entity/Ad.php(class qui contient tous les champs)
    //3èm param c'est l'instanciation de la Class
    public function show($slug, Ad $ad)
    {
        //Trouver un champ par rapport au slug(qui est la référence = ADN)
        //findOneBy = renvoyer un élémént         findBy = renvoyer plusieurs éléments dans un []
        // X = Slug = le nom du champ de référence 
        //$ad = $repo->findOneBySlug($slug);


        //Rajouter dans le tableau le champs(paramétré) récupéré
        //Création de formulaire 
        return $this->render('ad/show.html.twig', ['ad' => $ad]);
    }


    /**
     * Permet d'éditer et modifier une annonce par rapport à son ADN slug à partir d'un formulaire
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @return Response
     */
    public function edit(Ad $ad,Request $request,ObjectManager $manager){

        //Instanciation de la nouvelle Class form de formulaire(src/Form/AnnonceType.php)
        $form = $this->createForm(AnnonceType::class, $ad);
        //Récupération des données qui on été récupérées par $POST
        $form->handleRequest($request);
        //Toujours vérifier la soumission et validation d'un formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Faire une boucle pour récuper toutes les images récupérées dans le formulaire de collection append
            foreach ($ad->getYes() as $image) {
                # modifier l'image dans l'Entity (Ad.php) càd rélié à l'annonce
                $image->setAd($ad);
                #demande à Doctrine de sauvegarder ces données dans la BD dans l'Objet $manager(Injection de dépendance)
                $manager->persist($image);
            }
            //Si soumission ok et validation ok, on demande à Doctrine de sauvegarder ces données dans la BD dans l'Objet $manager(Injection de dépendance)
            $manager->persist($ad);
            //Envoyer les données(la requête) comme dans fixtures
            $manager->flush();
            //Envoye un message flash
            $this->addFlash("success", "Les modifications on été faites!");
            //Rediriger pour afficher une annonce par rapport à son ADN(slug)
            return $this->redirectToRoute('ads_single', ['slug' => $ad->getSlug()]);
        }
        
        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);
    }


}
