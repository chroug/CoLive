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
        $reservation = new Reservation();
        $reservation->setAnnounce($announce);
        $reservation->setLocataire($this->getUser());

        $unavailableDates = [];
        foreach ($announce->getReservations() as $res) {
            if ($res->getStatut() !== 'CANCELLED') {
                $unavailableDates[] = [
                    'from' => $res->getDateDebut()->format('Y-m-d'),
                    'to'   => $res->getDateFin()->format('Y-m-d'),
                ];
            }
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $overlaps = $em->getRepository(Reservation::class)->findOverlappingReservations(
                $announce,
                $reservation->getDateDebut(),
                $reservation->getDateFin()
            );

            if (count($overlaps) > 0) {
                $this->addFlash('danger', 'Désolé, ces dates viennent d’être réservées.');
            } else {
                $em->persist($reservation);
                $em->flush();
                $this->addFlash('success', 'Votre demande de réservation a bien été envoyée !');
                return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
            }
        }

        return $this->render('reservation/book.html.twig', [
            'form' => $form->createView(),
            'announce' => $announce,
            'unavailableDates' => json_encode($unavailableDates),
            'minDateCalculated' => new \DateTime() > $announce->getDisponibiliteDebut() ? new \DateTime() : $announce->getDisponibiliteDebut()
        ]);
    }

    #[Route('/reservation/{id}/cancel', name: 'app_reservation_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        if ($reservation->getLocataire() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Action interdite.");
        }

        if ($this->isCsrfTokenValid('cancel'.$reservation->getId(), $request->request->get('_token'))) {
            $reservation->setStatut('CANCELLED');
            $em->flush();
            $this->addFlash('success', 'La réservation a été annulée. Les dates sont à nouveau disponibles.');
        }

        return $this->redirectToRoute('app_profile');
    }
}
