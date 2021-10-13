<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\Pagination;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * Page pour afficher toutes les réservations à modérer 
     *
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_list")
     * @return Response
     */
    public function index(BookingRepository $repo,$page,Pagination $paginationService): Response
    {
        $paginationService->setEntityClass(Booking::class)
                          ->setPage($page)
                          //->setRoute('admin_bookings_list')
                          ;


        return $this->render('admin/booking/index.html.twig', [
            'pagination'=>$paginationService
        ]);
    }


    /**
     * Page pour modifier la réservation
     *
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Booking $booking,Request $request,ObjectManager $manager)
    {
        $id = $booking->getId();
        //Créer et peulpler un formulaire à partir du nouveau Entity AdmiBookingType
        $form = $this->createForm(AdminBookingType::class,$booking);
        //Faire la requête avec POST
        $form->handleRequest($request);
        //Sécurisé le formulaire 
        if($form->isSubmitted() && $form->isValid()){
            //Initialiser le montant à 0 pour être re-calculer(dans Entity Booking on a rajouté une annotaion preupdate dans la function prepersist())
            $booking->setAmount(0);
            //Enregistrer les données et dans la BD
            $manager->persist($booking);
            $manager->flush();
            //Message succès et redirection 
            $this->addFlash("success","La réservation n° <strong>{$id}</strong> a été modifié");
            return $this->redirectToRoute("admin_bookings_list");
        }
        return $this->render("admin/booking/edit.html.twig",[
            'booking'=>$booking,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Page pour supprimer une réservation
     *
     * @Route("/admin/booking{id}/delete", name="admin_booking_delete")
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager){
        $id = $booking->getId();
        //Suppression direct et enregistrer dans la BD
        $manager->remove($booking);
        $manager->flush();
        //Message de succès et redirection vers la page de toute la liste des réservations
        $this->addFlash("success","La réservation n° <strong>{$id}</strong> a été suprimé ");
        return $this->redirectToRoute("admin_bookings_list");
    }

}
