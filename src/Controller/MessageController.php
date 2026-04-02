<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    #[Route('/message/{id}', name: 'app_message_conversation')]
    public function index(?int $id, MessageRepository $messageRepository, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $baseContacts = $currentUser->getContacts()->toArray();
        $searchTerm = $request->query->get('q');
        $users = [];

        $allUserMessages = $messageRepository->findUserDiscussions($currentUser);

        $unreadSenders = [];
        foreach ($allUserMessages as $msg) {
            if (!$msg->isRead() && $msg->getRecipient() === $currentUser) {
                $unreadSenders[$msg->getSender()->getId()] = true;
            }
        }

        $orderedContacts = [];
        foreach ($allUserMessages as $msg) {
            $otherUser = ($msg->getSender() === $currentUser) ? $msg->getRecipient() : $msg->getSender();
            if (!in_array($otherUser, $orderedContacts, true)) {
                $orderedContacts[] = $otherUser;
            }
        }

        $allContacts = array_unique(array_merge($orderedContacts, $baseContacts), SORT_REGULAR);

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
                                $this->addFlash('danger', 'Erreur lors de l\'envoi du fichier.');
                            }
                        }

                        $entityManager->persist($message);
                        $entityManager->flush();

                        return $this->redirectToRoute('app_message_conversation', ['id' => $selectedUser->getId()]);
                    }
                }

                $messages = $messageRepository->findConversation($currentUser, $selectedUser);

                $hasUnread = false;
                foreach ($messages as $msg) {
                    if ($msg->getRecipient()->getId() === $currentUser->getId() && !$msg->isRead()) {
                        $msg->setIsRead(true);
                        $hasUnread = true;
                    }
                }

                if ($hasUnread) {
                    $entityManager->flush();

                    if (isset($unreadSenders[$selectedUser->getId()])) {
                        unset($unreadSenders[$selectedUser->getId()]);
                    }
                }

                $latestResId = null;
                foreach ($messages as $msg) {
                    if ($msg->getContent() !== null && preg_match('/\[RES_ID:(\d+)]/', $msg->getContent(), $matches)) {
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
            'unreadSenders' => $unreadSenders,
        ]);
    }

    public function unreadCount(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new Response('');
        }

        $count = $em->getRepository(Message::class)->count([
            'recipient' => $user,
            'isRead' => false
        ]);

        return $this->render('message/_badge.html.twig', [
            'count' => $count
        ]);
    }
}
