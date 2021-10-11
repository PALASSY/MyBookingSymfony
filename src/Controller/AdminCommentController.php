<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * Page qui affiche tous les commentaires à modérer
     * 
     * @Route("/admin/comments", name="admin_comments_list")
     * 
     */
    public function index(CommentRepository $repo): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll()
        ]);
    }


    /**
     * Page qui modifie le commentaire de l'USER
     *
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Comment $comment,Request $request,ObjectManager $manager)
    {
        //crée un formulaire à partir des commentaire 
        $form = $this->createForm(CommentType::class,$comment);
        //Faire la requête avec POST
        $form->handleRequest($request);
        //Sécurisé le formulaire 
        if ($form->isSubmitted() && $form->isValid()) {
            //Enregistre les données 
            $manager->persist($comment);
            //Enregistrer dans la BD 
            $manager->flush();
            //Envoyer un message de succès
            $this->addFlash("success","Le commentaire N°<strong>{$comment->getId()}</strong> a été modifié");
            //Renvoyer à la liste des commentaires 
            return $this->redirectToRoute("admin_comments_list");
        }
        return $this->render("admin/comment/edit.html.twig", [
            "comment"=>$comment,
            "form"=>$form->createView()
        ]);
    }


    /**
     * Page qui supprime le commentaire de l'USER
     * 
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment,ObjectManager $manager)
    {
        $numero = $comment->getId();
        //supprimer directement le commentaire 
        $manager->remove($comment);
        $manager->flush();
        //Message de succès et redirection
        $this->addFlash("succes", "Le commentaire portant le n°<strong>{$numero}</strong> a été supprimé");
        return $this->redirectToRoute("admin_comments_list");
    }
}
