<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Reservation;
use App\Entity\Message;
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
        $tenant = $this->getUser();
        $reservation->setLocataire($tenant);
        $host = $announce->getUtilisateur();

        $unavailableDates = [];

        $bookedDates = [];
        $pendingDates = [];

        $bookedDates = [];

        foreach ($announce->getReservations() as $res) {
            if ($res->getStatut() === 'CONFIRMED') {
                $bookedDates[] = [
                    'from' => $res->getDateDebut()->format('Y-m-d'),
                    'to'   => $res->getDateFin() ? $res->getDateFin()->format('Y-m-d') : '2099-12-31',
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
                $this->addFlash('danger', 'Désolé, ces dates sont déjà réservées.');
            } else {
                $em->persist($reservation);
                $em->flush();

                if ($host && $host !== $tenant) {
                    if (!$tenant->getContacts()->contains($host)) $tenant->addContact($host);
                    if (!$host->getContacts()->contains($tenant)) $host->addContact($tenant);

                    $autoMessage = new Message();
                    $autoMessage->setSender($tenant);
                    $autoMessage->setRecipient($host);

                    $autoMessage->setContent(sprintf(
                        "[RES_ID:%d] Nouvelle demande de réservation pour '%s' du %s au %s.",
                        $reservation->getId(),
                        $announce->getTitre(),
                        $reservation->getDateDebut()->format('d/m/Y'),
                        $reservation->getDateFin()->format('d/m/Y')
                    ));

                    $em->persist($autoMessage);
                    $em->flush();
                }

                $this->addFlash('success', 'Votre demande de réservation a bien été envoyée !');
                return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
            }
        }

        return $this->render('reservation/book.html.twig', [
            'announce' => $announce,
            'form' => $form->createView(),
            'bookedDates' => $bookedDates,
        ]);
    }

    /**
     * ROUTE D'ANNULATION
     */
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
            $this->addFlash('success', 'La réservation a été annulée avec succès.');
        }

        return $this->redirectToRoute('app_profile');
    }

    /**
     * ACCEPTER DEPUIS LA MESSAGERIE
     */
    #[Route('/reservation/{id}/accept', name: 'app_reservation_accept')]
    #[IsGranted('ROLE_USER')]
    public function accept(Reservation $reservation, EntityManagerInterface $em): Response
    {
        if ($reservation->getAnnounce()->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $reservation->setStatut('CONFIRMED');

        $msg = new Message();
        $msg->setSender($this->getUser());
        $msg->setRecipient($reservation->getLocataire());

        $msg->setContent("[RES_ACCEPT] J'ai accepté votre réservation pour '" . $reservation->getAnnounce()->getTitre() . "'. À bientôt !");

        $em->persist($msg);
        $em->flush();

        $this->addFlash('success', 'Réservation confirmée.');
        return $this->redirectToRoute('app_message_conversation', ['id' => $reservation->getLocataire()->getId()]);
    }

    /**
     * REFUSER DEPUIS LA MESSAGERIE
     */
    #[Route('/reservation/{id}/reject', name: 'app_reservation_reject')]
    #[IsGranted('ROLE_USER')]
    public function reject(Reservation $reservation, EntityManagerInterface $em): Response
    {
        if ($reservation->getAnnounce()->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $reservation->setStatut('CANCELLED');

        $msg = new Message();
        $msg->setSender($this->getUser());
        $msg->setRecipient($reservation->getLocataire());

        $msg->setContent("[RES_REJECT] Désolé, je ne peux pas accepter votre réservation pour '" . $reservation->getAnnounce()->getTitre() . "'.");

        $em->persist($msg);
        $em->flush();

        $this->addFlash('danger', 'Réservation refusée.');
        return $this->redirectToRoute('app_message_conversation', ['id' => $reservation->getLocataire()->getId()]);
    }
}
