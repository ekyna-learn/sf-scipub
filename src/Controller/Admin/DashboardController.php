<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * Dashboard action
     *
     * @param PublicationRepository $publicationRepository
     * @param CommentRepository $commentRepository
     *
     * @Route("/admin", name="admin_dashboard")
     */
    public function __invoke(
        PublicationRepository $publicationRepository,
        CommentRepository $commentRepository
    ): Response {
        return $this->render('Admin/Dashboard/index.html.twig', [
            'publications' => $publicationRepository->findNotValidated(),
            'comments' => $commentRepository->findNotValidated(),
        ]);
    }
}
