<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Reservation;
use App\Entity\Message;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
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
    #[Route('/announce/{id}/reserve', name: 'app_reservation_reserve', methods: ['GET', 'POST'])]
    public function reserve(Announce $announce, Request $request, EntityManagerInterface $em, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $reservation->setAnnounce($announce);
        $tenant = $this->getUser();
        $reservation->setLocataire($tenant);
        $host = $announce->getUtilisateur();

        $bookedDates = [];

        foreach ($announce->getReservations() as $res) {
            if ($res->getStatut() === 'CONFIRMED') {
                $bookedDates[] = [
                    'from' => $res->getDateDebut()->format('Y-m-d'),
                    'to'   => $res->getDateFin() ? $res->getDateFin()->format('Y-m-d') : '2099-12-31',
                ];
            }
        }

        $myReservations = [];
        if ($tenant) {
            foreach ($announce->getReservations() as $res) {
                if ($res->getLocataire() === $tenant && $res->getStatut() !== 'CANCELLED') {
                    $myReservations[] = $res;
                }
            }
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userOverlaps = $reservationRepository->findUserOverlappingReservations(
                $announce,
                $tenant,
                $reservation->getDateDebut(),
                $reservation->getDateFin()
            );

            if (count($userOverlaps) > 0) {
                $this->addFlash('danger', 'Vous avez déjà une demande de réservation en cours pour ces dates.');
                return $this->redirect($request->headers->get('referer'));
            }

            $em->persist($reservation);
            $em->flush();

            if ($host && $host !== $tenant) {
                if (!$tenant->getContacts()->contains($host)) $tenant->addContact($host);
                if (!$host->getContacts()->contains($tenant)) $host->addContact($tenant);

                $autoMessage = new Message();
                $autoMessage->setSender($tenant);
                $autoMessage->setRecipient($host);


                $dateFinString = $reservation->getDateFin() ? $reservation->getDateFin()->format('d/m/Y') : 'une durée indéterminée';

                $autoMessage->setContent(sprintf(
                    "[RES_ID:%d] Nouvelle demande de réservation pour '%s' du %s au %s.",
                    $reservation->getId(),
                    $announce->getTitre(),
                    $reservation->getDateDebut()->format('d/m/Y'),
                    $dateFinString
                ));

                $em->persist($autoMessage);
                $em->flush();
            }

            $this->addFlash('success', 'Votre demande de réservation a bien été envoyée !');
            return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
        }

        return $this->render('reservation/book.html.twig', [
            'announce' => $announce,
            'form' => $form->createView(),
            'bookedDates' => $bookedDates,
            'myReservations' => $myReservations,
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
     * REFUSER DEPUIS LA MESSAGERIE
     */
    #[Route('/reservation/{id}/reject', name: 'app_reservation_reject', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reject(Request $request, Reservation $reservation, EntityManagerInterface $em): Response
    {
        if ($reservation->getAnnounce()->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('reject'.$reservation->getId(), $request->request->get('_token'))) {

            if ($reservation->getStatut() !== 'PENDING') {
                $this->addFlash('danger', 'Erreur : Cette demande a déjà été traitée.');
                return $this->redirect($request->headers->get('referer'));
            }

            $reservation->setStatut('CANCELLED');

            $msg = new Message();
            $msg->setSender($this->getUser());
            $msg->setRecipient($reservation->getLocataire());

            $msg->setContent("[RES_REJECT] Désolé, je ne peux pas accepter votre réservation pour '" . $reservation->getAnnounce()->getTitre() . "'.");

            $em->persist($msg);
            $em->flush();

            $this->addFlash('success', 'La réservation a été refusée.');
        } else {
            $this->addFlash('danger', 'Token de sécurité invalide.');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/reservation/{id}/accept', name: 'app_reservation_accept', methods: ['POST'])]
    public function accept(Request $request, Reservation $reservation, EntityManagerInterface $em, ReservationRepository $repo): Response
    {
        $host = $reservation->getAnnounce()->getUtilisateur();

        if ($host !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de cette annonce.');
        }

        if ($this->isCsrfTokenValid('accept'.$reservation->getId(), $request->request->get('_token'))) {
            if ($reservation->getStatut() !== 'PENDING') {
                $this->addFlash('danger', 'Erreur : Cette demande a déjà été traitée (acceptée ou annulée suite à une autre réservation).');
                return $this->redirect($request->headers->get('referer'));
            }

            $reservation->setStatut('CONFIRMED');

            $tenant = $reservation->getLocataire();


            if (!$tenant->getContacts()->contains($host)) $tenant->addContact($host);
            if (!$host->getContacts()->contains($tenant)) $host->addContact($tenant);

            $acceptMessage = new Message();
            $acceptMessage->setSender($host);
            $acceptMessage->setRecipient($tenant);

            $dateFinStr = $reservation->getDateFin() ? $reservation->getDateFin()->format('d/m/Y') : 'une durée indéterminée';

            $acceptMessage->setContent(sprintf(
                "[RES_ACCEPT] Bonne nouvelle ! J'ai accepté votre demande de réservation pour '%s' du %s au %s.",
                $reservation->getAnnounce()->getTitre(),
                $reservation->getDateDebut()->format('d/m/Y'),
                $dateFinStr
            ));

            $em->persist($acceptMessage);

            $overlaps = $repo->findPendingOverlaps($reservation);

            $aStart = $reservation->getDateDebut();
            $aEnd = $reservation->getDateFin();

            foreach ($overlaps as $pending) {
                $pending->setStatut('CANCELLED');

                $tenant = $pending->getLocataire();
                $pStart = $pending->getDateDebut();
                $pEnd = $pending->getDateFin();

                $messageContent = "";
                $propStart = null;
                $propEnd = null;

                if ($pStart >= $aStart && ($aEnd === null || ($pEnd !== null && $pEnd <= $aEnd))) {
                    $messageContent = "Bonjour. Malheureusement, j'ai accepté une autre réservation qui couvre exactement vos dates. Votre demande a donc été annulée.";
                } elseif ($pStart < $aStart && $pEnd <= $aEnd) {
                    $propStart = $pStart;
                    $propEnd = $aStart;
                } elseif ($pStart >= $aStart && $pStart < $aEnd && ($pEnd > $aEnd || $pEnd === null)) {
                    $propStart = $aEnd;
                    $propEnd = $pEnd;
                } elseif ($pStart < $aStart && ($pEnd > $aEnd || $pEnd === null)) {
                    $propStart = $pStart;
                    $propEnd = $aStart;
                }

                if ($propStart && $propEnd) {
                    $messageContent = sprintf(
                        "Bonjour. J'ai accepté une autre demande du %s au %s. Cependant, mon logement reste disponible du %s au %s ! Si cela vous convient, n'hésitez pas à refaire une demande pour ces nouvelles dates.",
                        $aStart->format('d/m/Y'),
                        $aEnd ? $aEnd->format('d/m/Y') : 'une durée indéterminée',
                        $propStart->format('d/m/Y'),
                        $propEnd->format('d/m/Y')
                    );
                } elseif ($propStart && !$propEnd) {
                    $messageContent = sprintf(
                        "Bonjour. J'ai accepté une autre demande du %s au %s. Cependant, mon logement est disponible à partir du %s pour une durée indéterminée ! Si cela vous convient, refaites une demande.",
                        $aStart->format('d/m/Y'),
                        $aEnd ? $aEnd->format('d/m/Y') : 'une durée indéterminée',
                        $propStart->format('d/m/Y')
                    );
                }

                if ($messageContent !== "") {
                    if (!$tenant->getContacts()->contains($host)) $tenant->addContact($host);
                    if (!$host->getContacts()->contains($tenant)) $host->addContact($tenant);

                    $autoMessage = new Message();
                    $autoMessage->setSender($host);
                    $autoMessage->setRecipient($tenant);
                    $autoMessage->setContent($messageContent);

                    $em->persist($autoMessage);
                }
            }

            $em->flush();
            $this->addFlash('success', 'La réservation a été acceptée avec succès. Les autres demandes ont été annulées et les clients prévenus.');
        } else {
            $this->addFlash('danger', 'Token de sécurité invalide.');
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
