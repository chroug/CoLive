<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact/ajouter/{id}', name: 'app_contact_add')]
    public function add(User $userToAdd, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser && $userToAdd) {
            $currentUser->addContact($userToAdd);

            $em->persist($currentUser);
            $em->flush();

            $this->addFlash('success', 'Contact ajouté ! Vous pouvez discuter.');
        }
        return $this->redirectToRoute('app_messagerie_conversation', ['id' => $userToAdd->getId()]);
    }

    #[Route('/contact/supprimer/{id}', name: 'app_contact_remove')]
    public function remove(User $userToRemove, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser) {
            $currentUser->removeContact($userToRemove);

            $em->persist($currentUser);
            $em->flush();

            $this->addFlash('success', 'Discussion supprimée.');
        }

        return $this->redirectToRoute('app_messagerie');
    }
}
