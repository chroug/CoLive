<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Review;
use App\Repository\AnnounceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(AnnounceRepository $announceRepo, EntityManagerInterface $em): Response
    {
        $pendingAnnounces = $announceRepo->findBy(['isValidated' => false]);
        $reviews = $em->getRepository(Review::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'pendingAnnounces' => $pendingAnnounces,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/announce/{id}/validate', name: 'app_admin_announce_validate')]
    public function validateAnnounce(Announce $announce, EntityManagerInterface $em): Response
    {
        $announce->setIsValidated(true);
        $em->flush();
        $this->addFlash('success', 'Annonce validée avec succès.');
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/announce/{id}/delete', name: 'app_admin_announce_delete')]
    public function deleteAnnounce(Announce $announce, EntityManagerInterface $em): Response
    {
        $em->remove($announce);
        $em->flush();
        $this->addFlash('danger', 'Annonce refusée et supprimée.');
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/review/{id}/delete', name: 'app_admin_review_delete')]
    public function deleteReview(Review $review, EntityManagerInterface $em): Response
    {
        $em->remove($review);
        $em->flush();
        $this->addFlash('success', 'Avis supprimé.');
        return $this->redirectToRoute('app_admin_dashboard');
    }
}
