<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $notifications = $em->getRepository(Notification::class)->findBy(
            ['recipient' => $user],
            ['createdAt' => 'DESC']
        );

        $hasUnread = false;
        foreach ($notifications as $notification) {
            if (!$notification->isRead()) {
                $notification->setIsRead(true);
                $hasUnread = true;
            }
        }

        if ($hasUnread) {
            $em->flush();
        }

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    public function unreadCount(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new Response('');
        }

        $count = $em->getRepository(Notification::class)->count([
            'recipient' => $user,
            'isRead' => false
        ]);

        return $this->render('notification/_badge.html.twig', [
            'count' => $count
        ]);
    }
}
