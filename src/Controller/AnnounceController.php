<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\UserLikes;
use App\Form\AnnounceType;
use App\Repository\AnnounceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AnnounceController extends AbstractController
{
    #[Route('/announce', name: 'app_announce')]
    public function index(AnnounceRepository $announceRepository, Request $request)
    {
        $location = $request->query->get('location');
        $type = $request->query->get('type');
        $dateStart = $request->query->get('date_start');
        $dateEnd = $request->query->get('date_end');
        $announces = $announceRepository->findByFilters($location, $type, $dateStart, $dateEnd);
        return $this->render('announce/index.html.twig', [
            'announces'=>$announces,
            'searchLocation' => $location,
            'searchType' => $type,
            'searchStart' => $dateStart,
            'searchEnd' => $dateEnd,
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/announce/create', name: 'app_announce_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Announce();
        $form = $this->createForm(AnnounceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUtilisateur($this->getUser());
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Votre announce a été publiée avec succès. Elle est désormais visible par les utilisateurs.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('announce/create.html.twig', [
            'controller_name' => 'AnnounceController',
            'formAnnonce' => $form->createView(),
        ]);
    }

    #[Route('/announce/{id}/like', name: 'app_announce_like')]
    public function like(Announce $announce, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Non autorisé'], 403);
        }
        $like = $entityManager->getRepository(UserLikes::class)->findOneBy([
            'utilisateur' => $user,
            'annonce' => $announce
        ]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
            return $this->json(['isLiked' => false]);
        }
        $newLike = new UserLikes();
        $newLike->setUtilisateur($user);
        $newLike->setAnnonce($announce);

        $entityManager->persist($newLike);
        $entityManager->flush();

        return $this->json(['isLiked' => true]);
    }
}
