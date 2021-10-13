<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use App\Service\Pagination;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminAdController extends AbstractController
{
    /**
     * Page qui affiche toutes les annonces à modérer 
     * Ici il faut mettre le requirements pour utilisé le 2em param de la function et le restreindre
     *
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_list")
     * 
     * @return Response
     */
    public function index(AdRepository $repo,$page, Pagination $paginationService): Response
    {

        //On va setter l'Entity et la page actuelle iniatiale
        $paginationService->setEntityClass(Ad::class)
                          ->setPage($page)
                          //->setRoute('admin_ads_list')
                          ;

        //Rétourner le service pagination (Entity personnalisée ,les nombres des pages et la page initiale)
        return $this->render('admin/ad/index.html.twig', [
            'pagination'=>$paginationService
        ]);
    }



    /**
     * Modifier une annonce de l'USER 
     * @Route("/admin/ad/{id}/edit", name="admin_ad_edit")
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad,Request $request,ObjectManager $manager)
    {
        // Créer et peuler le formulaire
        $form = $this->createForm(AnnonceType::class,$ad);
        //Faire la requête avec POST
        $form->handleRequest($request);
        //Sécurisé le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //On va enregistrer les données dee l'annonce
            $manager->persist($ad);
            //Enregistrer les données dans BD
            $manager->flush();
            //Envoyé un message de succès
            $this->addFlash("success","L'annonce a été modifiée et ajoutée dans BD");
            //Rédiriger vers la liste des annonces 
            return $this->redirectToRoute("admin_ads_list");
        }
        return $this->render("admin/ad/edit.html.twig",['ad'=>$ad,'form'=>$form->createView()]);
    }



    /**
     * Supprimer une annonce de l'USER
     * 
     * @Route("/admin/ad/{id}/delete", name="admin_ad_delete")
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        // si l'annonce possède une reservation, on ne peut pas supprimer
        if (count($ad->getBookings($ad)) > 0) {
            $this->addFlash("warning", "Vous ne pouvez supprimer une annonce qui possède une ou des réservations");
        } else {
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash("success", "L'annonce au titre <strong>{$ad->getTitle()}</strong> a été supprimée");
        }
            return $this->redirectToRoute("admin_ads_list");
    }



}
