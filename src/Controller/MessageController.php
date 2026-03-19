<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ReservationRepository; // NOUVEAU : On importe le repository des réservations
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    #[Route('/message/{id}', name: 'app_message_conversation')]
    public function index(?int $id, MessageRepository $messageRepository, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }
        $allContacts = $currentUser->getContacts();
        $searchTerm = $request->query->get('q');
        $users = [];

        if ($searchTerm) {
            foreach ($allContacts as $contact) {
                if (stripos($contact->getNom(), $searchTerm) !== false ||
                    stripos($contact->getPrenom(), $searchTerm) !== false) {
                    $users[] = $contact;
                }
            }
        } else {
            $users = $allContacts;
        }

        $selectedUser = null;
        $messages = [];
        $activeReservation = null;

        if ($id) {
            $selectedUser = $entityManager->getRepository(User::class)->find($id);

            if ($selectedUser) {
                if ($request->isMethod('POST')) {
                    $content = $request->request->get('content');
                    $file = $request->files->get('file_upload');

                    if (!empty($content) || $file) {
                        $message = new Message();
                        $message->setContent($content);
                        $message->setSender($currentUser);
                        $message->setRecipient($selectedUser);

                        if ($file) {
                            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                            try {
                                $file->move($uploadDir, $fileName);
                                $message->setAttachment($fileName);
                            } catch (\Exception $e) {
                            }
                        }

                        $entityManager->persist($message);
                        $entityManager->flush();

                        return $this->redirectToRoute('app_message_conversation', ['id' => $selectedUser->getId()]);
                    }
                }

                $messages = $messageRepository->findConversation($currentUser, $selectedUser);

                $latestResId = null;
                foreach ($messages as $msg) {
                    if (preg_match('/\[RES_ID:(\d+)\]/', $msg->getContent(), $matches)) {
                        $latestResId = (int) $matches[1];
                    }
                }

                if ($latestResId) {
                    $activeReservation = $reservationRepository->find($latestResId);
                }
            }
        }

        return $this->render('message/index.html.twig', [
            'users' => $users,
            'selectedUser' => $selectedUser,
            'messages' => $messages,
            'searchTerm' => $searchTerm,
            'activeReservation' => $activeReservation,
        ]);
    }
}
