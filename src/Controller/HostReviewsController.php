<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HostReviewsController extends AbstractController
{
    #[Route('/hote/{id}/avis', name: 'app_host_reviews')]
    public function index(User $host): Response
    {
        $reviewsRecus = [];
        $totalNote = 0;
        $countNotes = 0;

        foreach ($host->getAnnonces() as $annonce) {
            foreach ($annonce->getAvis() as $avis) {
                $reviewsRecus[] = $avis;
                $totalNote += $avis->getNote();
                $countNotes++;
            }
        }

        usort($reviewsRecus, function($a, $b) {
            return $b->getDateCreation() <=> $a->getDateCreation();
        });

        $averageRating = $countNotes > 0 ? $totalNote / $countNotes : 0;

        return $this->render('host_reviews/index.html.twig', [
            'host' => $host,
            'reviews' => $reviewsRecus,
            'averageRating' => number_format($averageRating, 1),
            'totalReviews' => $countNotes
        ]);
    }
}
