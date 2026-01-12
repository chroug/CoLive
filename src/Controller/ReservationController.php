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
        // 1. Initialisation de la réservation
        $reservation = new Reservation();
        $reservation->setAnnounce($announce);
        $reservation->setLocataire($this->getUser());

        // Pré-remplissage avec les dates de l'annonce par défaut
        $reservation->setDateDebut($announce->getDisponibiliteDebut());
        $reservation->setDateFin($announce->getDisponibiliteFin());

        // 2. Création du formulaire
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // 3. Traitement
        if ($form->isSubmitted() && $form->isValid()) {

            // Vérification simple : dates dans la plage de l'annonce
            if ($reservation->getDateDebut() < $announce->getDisponibiliteDebut() ||
                $reservation->getDateFin() > $announce->getDisponibiliteFin()) {

                $this->addFlash('danger', 'Les dates choisies sont en dehors des disponibilités de l\'annonce.');

            } else {
                $em->persist($reservation);
                $em->flush();

                $this->addFlash('success', 'Votre demande de réservation a bien été envoyée !');
                return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
            }
        }

        return $this->render('reservation/book.html.twig', [
            'form' => $form->createView(),
            'announce' => $announce
        ]);
    }
}
