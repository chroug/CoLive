<?php

namespace App\Controller;

use App\Repository\AnnounceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(AnnounceRepository $announceRepo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $mesAnnonces = $announceRepo->findBy(['utilisateur' => $user]);
        $reservationsEnCours = [];
        $reservationsAVenir = [];
        $aujourdhui = new \DateTime();

        foreach ($mesAnnonces as $annonce) {
            foreach ($annonce->getReservations() as $reservation) {
                if ($reservation->getDateDebut() <= $aujourdhui && $reservation->getDateFin() >= $aujourdhui) {
                    $reservationsEnCours[] = $reservation;
                }
                elseif ($reservation->getDateDebut() > $aujourdhui) {
                    $reservationsAVenir[] = $reservation;
                }
            }
        }

        usort($reservationsAVenir, function($a, $b) {
            return $a->getDateDebut() <=> $b->getDateDebut();
        });

        return $this->render('dashboard/index.html.twig', [
            'mes_annonces' => $mesAnnonces,
            'reservations_en_cours' => $reservationsEnCours,
            'reservations_a_venir' => $reservationsAVenir,
        ]);
    }
}
