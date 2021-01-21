<?php

namespace App\Controller\Admin;

use App\Entity\Publication;
use App\Form\ConfirmType;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/publication", name="admin_publication_")
 */
class PublicationController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(PublicationRepository $publicationRepository): Response
    {
        return $this->render('Admin/Publication/index.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication, [
            'admin' => true,
        ]);
        $this->addSubmitButton($form, 'Créer', 'success');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('admin_publication_index');
        }

        return $this->render('Admin/Publication/new.html.twig', [
            'publication' => $publication,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Publication $publication): Response
    {
        return $this->render('Admin/Publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Publication $publication): Response
    {
        $form = $this->createForm(PublicationType::class, $publication, [
            'admin' => true,
        ]);
        $this->addSubmitButton($form, 'Modifier', 'warning');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_publication_index');
        }

        return $this->render('Admin/Publication/edit.html.twig', [
            'publication' => $publication,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Publication $publication): Response
    {
        $form = $this->createForm(ConfirmType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($publication);
            $entityManager->flush();

            $this->addFlash('success', 'La publication a bien été supprimée.');

            return $this->redirectToRoute('admin_publication_index');
        }

        return $this->render('Admin/Publication/delete.html.twig', [
            'publication' => $publication,
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
