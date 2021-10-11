<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends AbstractController
{
    /**
     * Permet afficher le formulaire de réservation
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @return Response
     */
    public function book(Ad $ad,Request $request,ObjectManager $manager): Response
    {
        //Instancier un nouvel Objet de Réservation 
        $booking = new Booking();

        //Créer et peupler le formulaire avec le nouvel Objet
        $form = $this->createForm(BookingType::class,$booking);
        
        //Récupération des données qui ont été récupérées par $post
        $form->handleRequest($request);

        //Assurer la sécurité de validation et de soumission
        if($form->isSubmitted() && $form->isValid()){

            //Il nous reste à récupérer la personne qui a fait la réservation 
            $user = $this->getUser();

            //Comme on a la personne qui reserve/l'annonce qui fait l'Objet de la reservation/La reservation qui a été instancier,alors il reste qu'à sette cette nouvel Objet 
            $booking->setbooker($user)
                    ->setAd($ad)
                    ;
            //Remarque: ici l'Objet Booking ne possède que la date de début de la réservation et la date de la fin de la reservation alors il faut créer dans l'Entity la date de création de réservation et le montant total de la réservation sans oublier la durée de la réservation et tout cela doit être fourni automatiquement par(LifeCycle Callback => PrePersit() => cycle de vie)


            //Avant d'enregistrer les données on va vérifier si les dates sont disponibles ou pas
            if(!$booking->isBookabledays()){
                $this->addFlash("warning","Ces dates ne sont pas disponibles, choisissez autres dates pour votre réservation");
            }else{

            //Enregister les données 
            $manager->persist($booking);

            //Envoyer les données dans la BD
            $manager->flush();

            //Rediriger vers la page. qui n'existe pas encore mais créee après cette page de réservation
            //Ici on va récupérer l'Id de la réservation et créer la variable alert et l'assigner à true
            //Créer une variable (alert) pour afficher le message flash en mode GET
            return $this->redirectToRoute("booking_show",['id'=>$booking->getId(),'alert'=>true]);
            }
        }
                
        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form'=>$form->createView()
        ]);
    }


    /**
     * Page résumé(détails) de la  réservation
     * La route comporte l'Id Booking(réservation)
     * @Route("/booking/{id}", name="booking_show")
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking,Request $request,ObjectManager $manager){
        //On va créer une commentaire 
        $comment = new Comment();
        //On va créer une formulaire de commentaire à partir de ce nouveau commentaire 
        $form = $this->createForm(CommentType::class,$comment);
        //Récupérer les données par $POST
        $form->handleRequest($request);
        //Assurer la sécurité de validation et soumission
        if ($form->isSubmitted() && $form->isValid()) {
            //On va setter dans le commentaire l'annonce et l'author (puisqu'on les a supprimer volontairement dans src/Form.CommentType.php)
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser())
                    ;

            //On va enregistrer les données 
            $manager->persist($comment);

            //Enregistrer les données dans BD
            $manager->flush();

            //Afficher un message flash
            $this->addFlash('success','Votre commentaire a été enregistré dans BD');
        }


        return $this->render("booking/show.html.twig",['booking'=>$booking,'form'=>$form->createView()]);
    }


}
