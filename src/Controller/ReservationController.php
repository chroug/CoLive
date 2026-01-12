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
}
