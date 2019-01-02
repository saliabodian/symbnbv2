<?php

namespace App\Controller;
use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\PaginationService;

class AdminCommentController extends AbstractController
{
    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("admin/comments/{id}/delete", name ="admin_comment_delete")
     *
     * @return void
     */
    public function delete(Comment $comment, ObjectManager $manager){

        $manager->remove($comment);
        
                $this->addFlash('success',
                        "Le commentaire <strong>n° {$comment->getId()}</strong> a bien été supprimé !");
        $manager->flush();

        return $this->redirectToRoute('admin_comments_index');
    }

    /**
     * Permet d'éditer un commentaire
     * @Route("admin/comments/{id}/edit", name="admin_comment_edit")
     * @param Comment $comment
     * @return Response
     */    
    public function edit(Comment $comment, Request $request, ObjectManager $manager){

        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "La modification du commentaire <strong>n°{$comment->getId()}</strong> s'est effectuée avec succés."
            );
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                   ->setLimit(5)
                   ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
