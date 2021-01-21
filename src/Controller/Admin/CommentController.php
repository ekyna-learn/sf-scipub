<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Publication;
use App\Form\ConfirmType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/comment", name="admin_comment_")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('Admin/Comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $comment = new Comment();

        if (0 < $publicationId = $request->query->getInt('publicationId')) {
            $publication = $this
                ->getDoctrine()
                ->getRepository(Publication::class)
                ->find($publicationId);

            if ($publication) {
                $comment->setPublication($publication);
            }
        }

        $form = $this->createForm(CommentType::class, $comment, [
            'admin' => true,
        ]);
        $this->addSubmitButton($form, 'Créer', 'success');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('Admin/Comment/new.html.twig', [
            'comment' => $comment,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('Admin/Comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment, [
            'admin' => true,
        ]);
        $this->addSubmitButton($form, 'Modifier', 'warning');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('Admin/Comment/edit.html.twig', [
            'comment' => $comment,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(ConfirmType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'La comment a bien été supprimée.');

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('Admin/Comment/delete.html.twig', [
            'comment' => $comment,
            'form'    => $form->createView(),
        ]);
    }

    private function addSubmitButton(FormInterface $form, string $label, string $theme)
    {
        $form->add('submit', SubmitType::class, [
            'label' => $label,
            'attr'  => [
                'class' => 'btn-' . $theme,
            ],
        ]);
    }
}
