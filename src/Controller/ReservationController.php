<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/announce/{id}/reserve', name: 'app_announce_reserve')]
    #[IsGranted('ROLE_USER')]
    public function reserve(Announce $announce, Request $request, EntityManagerInterface $em): Response
    {
        // ... (Ton code existant pour la fonction reserve reste ici inchangé) ...
        $reservation = new Reservation();
        $reservation->setAnnounce($announce);
        $reservation->setLocataire($this->getUser());

        $unavailableDates = [];
        $existingReservations = $announce->getReservations();

        foreach ($existingReservations as $res) {
            if ($res->getStatut() !== 'CANCELLED') {
                $unavailableDates[] = [
                    'from' => $res->getDateDebut()->format('Y-m-d'),
                    'to'   => $res->getDateFin()->format('Y-m-d'),
                ];
            }
        }
        $unavailableDatesJson = json_encode($unavailableDates);

        $today = new \DateTime();
        $minDate = $announce->getDisponibiliteDebut();
        if ($minDate < $today) {
            $minDate = $today;
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($reservation->getDateDebut() < $announce->getDisponibiliteDebut() ||
                $reservation->getDateFin() > $announce->getDisponibiliteFin()) {
                $this->addFlash('danger', 'Les dates choisies sont en dehors des disponibilités de l\'annonce.');
            }
            else {
                $overlaps = $em->getRepository(Reservation::class)->findOverlappingReservations(
                    $announce,
                    $reservation->getDateDebut(),
                    $reservation->getDateFin()
                );

                if (count($overlaps) > 0) {
                    $this->addFlash('danger', 'Désolé, ces dates sont déjà réservées par un autre utilisateur.');
                } else {
                    // IMPORTANT: On définit le statut initial si ce n'est pas fait dans le constructeur
                    // $reservation->setStatut('CONFIRMED');

                    $em->persist($reservation);
                    $em->flush();

                    $this->addFlash('success', 'Votre demande de réservation a bien été envoyée !');
                    return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
                }
            }
        }

        return $this->render('reservation/book.html.twig', [
            'form' => $form->createView(),
            'announce' => $announce,
            'unavailableDates' => $unavailableDatesJson,
            'minDateCalculated' => $minDate
        ]);
    }

    /**
     * NOUVELLE MÉTHODE POUR ANNULER
     */
    #[Route('/reservation/{id}/cancel', name: 'app_reservation_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        // 1. Sécurité : Vérifier que c'est bien le locataire qui annule
        if ($reservation->getLocataire() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas annuler une réservation qui ne vous appartient pas.");
        }

        // 2. Sécurité : Vérifier le token CSRF pour éviter les fausses requêtes
        if ($this->isCsrfTokenValid('cancel'.$reservation->getId(), $request->request->get('_token'))) {

            // 3. On passe le statut à CANCELLED (Soft Delete)
            // Cela permet de libérer les dates dans ta fonction 'reserve' plus haut
            $reservation->setStatut('CANCELLED');

            $em->flush();
            $this->addFlash('success', 'La réservation a été annulée avec succès.');
        } else {
            $this->addFlash('danger', 'Token de sécurité invalide.');
        }

        // 4. Redirection vers la page de profil (change 'app_user_profile' par le vrai nom de ta route profil)
        return $this->redirectToRoute('app_profile');
    }
}
