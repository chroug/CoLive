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

        $totalReservations = 0;
        $revenusMensuels = 0;

        foreach ($mesAnnonces as $annonce) {
            $reservations = $annonce->getReservations();

            foreach ($reservations as $reservation) {

                if ($reservation->getStatut() === 'CONFIRMED') {

                    $totalReservations++;

                    $dateFin = $reservation->getDateFin();

                    if ($reservation->getDateDebut() <= $aujourdhui && ($dateFin === null || $dateFin >= $aujourdhui)) {
                        $reservationsEnCours[] = $reservation;
                        $revenusMensuels += $annonce->getPrix();
                    }
                    elseif ($reservation->getDateDebut() > $aujourdhui) {
                        $reservationsAVenir[] = $reservation;
                    }
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
            'total_annonces' => count($mesAnnonces),
            'total_reservations' => $totalReservations,
            'revenus_mensuels' => $revenusMensuels,
        ]);
    }
}
