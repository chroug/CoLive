<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\AnnouncePicture;
use App\Entity\User;
use App\Entity\UserLikes;
use App\Form\AnnounceType;
use App\Repository\AnnounceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $annonce = new Announce();
        $form = $this->createForm(AnnounceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUtilisateur($this->getUser());
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fileContent = file_get_contents($image->getPathname());
                $base64 = base64_encode($fileContent);
                $mimeType = $image->getMimeType();
                $dataUri = 'data:' . $mimeType . ';base64,' . $base64;
                $picture = new AnnouncePicture();
                $picture->setContenu($dataUri);
                $picture->setAnnonce($annonce);
                $em->persist($picture);
            }
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Votre annonce a été publiée avec succès.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('announce/create.html.twig', [
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

        $likeRepo = $entityManager->getRepository(UserLikes::class);
        $like = $likeRepo->findOneBy([
            'utilisateur' => $user,
            'annonce' => $announce
        ]);

        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();

            $totalLikes = $likeRepo->count(['annonce' => $announce]);

            return $this->json([
                'isLiked' => false,
                'totalLikes' => $totalLikes
            ]);
        }

        $newLike = new UserLikes();
        /** @var User $user */
        $newLike->setUtilisateur($user);
        $newLike->setAnnonce($announce);

        $entityManager->persist($newLike);
        $entityManager->flush();

        $totalLikes = $likeRepo->count(['annonce' => $announce]);

        return $this->json([
            'isLiked' => true,
            'totalLikes' => $totalLikes
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/announce/{id}/edit', name: 'app_announce_edit')]
    public function edit(Announce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        if ($annonce->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier cette annonce.');
            return $this->redirectToRoute('app_profile');
        }

        $form = $this->createForm(AnnounceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fileContent = file_get_contents($image->getPathname());
                $base64 = base64_encode($fileContent);
                $mimeType = $image->getMimeType();
                $dataUri = 'data:' . $mimeType . ';base64,' . $base64;

                $picture = new AnnouncePicture();
                $picture->setContenu($dataUri);
                $picture->setAnnonce($annonce);
                $em->persist($picture);
            }

            $em->flush();

            $this->addFlash('success', 'Votre annonce a été mise à jour.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('announce/edit.html.twig', [
            'formAnnonce' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/announce/picture/{id}/delete', name: 'app_announce_picture_delete', methods: ['DELETE'])]
    public function deletePicture(AnnouncePicture $picture, EntityManagerInterface $em): JsonResponse
    {
        $annonce = $picture->getAnnonce();

        if ($annonce->getUtilisateur() !== $this->getUser()) {
            return $this->json(['error' => 'Action non autorisée'], 403);
        }

        $em->remove($picture);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/announce/{id}/delete', name: 'app_announce_delete', methods: ['POST'])]
    public function delete(Announce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        if ($annonce->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer une annonce qui ne vous appartient pas.');
            return $this->redirectToRoute('app_profile');
        }
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {

            $em->remove($annonce);
            $em->flush();

            $this->addFlash('success', 'L\'annonce a été supprimée avec succès.');
        } else {
            $this->addFlash('danger', 'Token de sécurité invalide.');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/announce/{id}', name: 'app_announce_show')]
    public function show(Announce $announce): Response
    {
        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
        ]);
    }

    #[Route('/announce/{id}/avis', name: 'app_avis_add', methods: ['GET', 'POST'])]
    public function addAvis(
        \App\Entity\Announce $announce,
        \Symfony\Component\HttpFoundation\Request $request,
        \Doctrine\ORM\EntityManagerInterface $entityManager
    ): \Symfony\Component\HttpFoundation\Response {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $avis = new \App\Entity\Review();

        $form = $this->createFormBuilder($avis)
            ->add('note', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices'  => [
                    '⭐⭐⭐⭐⭐ (5/5) - Parfait !' => 5,
                    '⭐⭐⭐⭐ (4/5) - Très bien' => 4,
                    '⭐⭐⭐ (3/5) - Bien' => 3,
                    '⭐⭐ (2/5) - Moyen' => 2,
                    '⭐ (1/5) - À fuir' => 1,
                ],
                'label' => 'Votre note globale'
            ])
            ->add('commentaire', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'label' => 'Racontez votre expérience (optionnel)',
                'required' => false,
                'attr' => ['rows' => 4, 'placeholder' => 'Le logement était super, très propre...']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $avis->setUtilisateur($user);
            $avis->setAnnonce($announce);

            $entityManager->persist($avis);

            $proprietaire = $announce->getUtilisateur();

            if ($proprietaire && $proprietaire !== $user) {
                $notification = new \App\Entity\Notification();
                $notification->setRecipient($proprietaire);
                $notification->setType('nouvel_avis');

                $message = sprintf(
                    '%s a laissé un avis (%d/5) sur votre logement "%s".',
                    $user->getPrenom(),
                    $avis->getNote(),
                    $announce->getTitre()
                );
                $notification->setContent($message);
                $entityManager->persist($notification);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a bien été publié, merci !');

            return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
        }

        return $this->render('announce/add_avis.html.twig', [
            'announce' => $announce,
            'form' => $form->createView(),
        ]);
    }
}
