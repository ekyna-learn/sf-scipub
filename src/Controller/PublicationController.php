<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Science;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use App\Repository\ScienceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    /**
     * Home action.
     *
     * @return Response
     *
     * @Route(
     *     "",
     *     name="publication_index",
     *     methods={"GET"}
     * )
     */
    public function index(): Response
    {
        $publications = $this
            ->getPublicationRepository()
            ->findLatest();

        return $this->render('Publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    /**
     * Sciences list action.
     *
     * @return Response
     *
     * @Route(
     *     "/sciences",
     *     name="publication_sciences",
     *     methods={"GET"}
     * )
     */
    public function scienceList(): Response
    {
        $sciences = $this->getScienceRepository()->findBy([], ['title' => 'ASC']);

        return $this->render('Publication/science-list.html.twig', [
            'sciences' => $sciences,
        ]);
    }

    /**
     * Science detail action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route(
     *     "/sciences/{scienceId}",
     *     name="publication_science",
     *     requirements={"scienceId": "\d+"},
     *     methods={"GET"}
     * )
     */
    public function scienceDetail(Request $request): Response
    {
        $science = $this->findScience($request);

        $publications = $this->getPublicationRepository()->findByScience($science);

        return $this->render('Publication/science-detail.html.twig', [
            'science'      => $science,
            'publications' => $publications,
        ]);
    }

    /**
     * Publication detail action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route(
     *     "/sciences/{scienceId}/{publicationId}",
     *     name="publication_publication",
     *     requirements={"scienceId": "\d+", "publicationId": "\d+"},
     *     methods={"GET"}
     * )
     */
    public function publicationDetail(Request $request): Response
    {
        $science = $this->findScience($request);
        $publication = $this->findPublication($request);

        if ($science !== $publication->getScience()) {
            throw $this->createNotFoundException();
        }

        return $this->render('Publication/publication-detail.html.twig', [
            'science'     => $science,
            'publication' => $publication,
        ]);
    }

    /**
     * Publish action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route(
     *     "/publish",
     *     name="publication_publish",
     *     methods={"GET", "POST"}
     * )
     */
    public function publish(Request $request): Response
    {
        $publication = new Publication();

        $form = $this->createForm(PublicationType::class, $publication);

        $form->add('submit', SubmitType::class, [
            'label' => 'Publier',
            'attr'  => [
                'class' => 'btn-primary',
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($publication);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre publication a bien été enregistrée et sera soumise ' .
                'à modération dans les plus brefs délais.'
            );

            return $this->redirectToRoute('publication_index');
        }

        return $this->render('Publication/publish.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds the science from the given request.
     *
     * @param Request $request
     *
     * @return Science
     */
    private function findScience(Request $request): Science
    {
        $id = $request->attributes->getInt('scienceId');

        $science = $this->getScienceRepository()->find($id);

        if (!$science) {
            throw $this->createNotFoundException('Science not found.');
        }

        return $science;
    }

    /**
     * Finds the publication from the given request.
     *
     * @param Request $request
     *
     * @return Publication
     */
    private function findPublication(Request $request): Publication
    {
        $id = $request->attributes->getInt('publicationId');

        $publication = $this->getPublicationRepository()->find($id);

        if (!$publication) {
            throw $this->createNotFoundException('Publication not found.');
        }

        return $publication;
    }

    /**
     * Returns the science repository.
     *
     * @return ScienceRepository
     */
    private function getScienceRepository(): ScienceRepository
    {
        return $this->getDoctrine()->getRepository(Science::class);
    }

    /**
     * Returns the publication repository.
     *
     * @return PublicationRepository
     */
    private function getPublicationRepository(): PublicationRepository
    {
        return $this->getDoctrine()->getRepository(Publication::class);
    }
}
