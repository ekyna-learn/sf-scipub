<?php

namespace App\Controller\Admin;

use App\Entity\Publication;
use App\Entity\Science;
use App\Form\ConfirmType;
use App\Form\ScienceType;
use App\Repository\ScienceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/science", name="admin_science_")
 */
class ScienceController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ScienceRepository $scienceRepository): Response
    {
        return $this->render('Admin/Science/index.html.twig', [
            'sciences' => $scienceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $science = new Science();
        $form = $this->createForm(ScienceType::class, $science);
        $this->addSubmitButton($form, 'Créer', 'success');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($science);
            $entityManager->flush();

            return $this->redirectToRoute('admin_science_index');
        }

        return $this->render('Admin/Science/new.html.twig', [
            'science' => $science,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Science $science): Response
    {
        return $this->render('Admin/Science/show.html.twig', [
            'science' => $science,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Science $science): Response
    {
        $form = $this->createForm(ScienceType::class, $science);
        $this->addSubmitButton($form, 'Modifier', 'warning');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_science_index');
        }

        return $this->render('Admin/Science/edit.html.twig', [
            'science' => $science,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, Science $science): Response
    {
        $form = $this->createForm(ConfirmType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->shouldPreventRemoval($science)) {
                $this->addFlash('danger', 'Cette science est lié à des publications et ne peut être supprimée.');
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($science);
                $entityManager->flush();

                $this->addFlash('success', 'La science a bien été supprimée.');
            }


            return $this->redirectToRoute('admin_science_index');
        }


        return $this->render('Admin/Science/delete.html.twig', [
            'science' => $science,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * Returns whether the science deletion should be prevented.
     *
     * @param Science $science
     *
     * @return bool
     */
    private function shouldPreventRemoval(Science $science): bool
    {
        $publication = $this
            ->getDoctrine()
            ->getRepository(Publication::class)
            ->findOneBy(['science' => $science]);

        return null !== $publication;
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
