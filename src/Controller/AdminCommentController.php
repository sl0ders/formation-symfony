<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comment")
     * @param CommentRepository $repository
     * @return Response
     */
    public function index(CommentRepository $repository)
    {
        $comments = $repository->findAll();
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/edit",name="admin_comment_edit")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @param Request $request
     * @return Response
     */
    public function edit(Comment $comment, ObjectManager $manager, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "le commentaire n°<strong>{$comment->getId()}</strong> à bien etait enregistrée"
            );
            return $this->redirectToRoute('admin_comment');
        }
        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/admin/comments/{id}/delete",name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return RedirectResponse
     */
    public function delete(Comment $comment, ObjectManager $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "le commentaire n°<strong>{$comment->getAuthor()->getFullName()}</strong> a bien etait supprimé"
        );
        return $this->redirectToRoute('admin_comment');
    }
}
