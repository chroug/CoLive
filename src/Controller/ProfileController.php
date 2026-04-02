<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Announce;
use App\Entity\UserLikes;
use App\Repository\ReservationRepository;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $totalNote = 0;
        $countNotes = 0;

        foreach ($user->getAnnonces() as $annonce) {
            foreach ($annonce->getAvis() as $avis) {
                $totalNote += $avis->getNote();
                $countNotes++;
            }
        }

        $averageRating = $countNotes > 0 ? $totalNote / $countNotes : 0;

        $form = $this->createFormBuilder($user)
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom']
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'exemple@email.com']
            ])
            ->add('tel', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => '06 12 34 56 78']
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Changer ma photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPG, PNG, WEBP)',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('avatarFile')->getData();

            if ($uploadedFile) {
                $fileContent = file_get_contents($uploadedFile->getPathname());
                $base64 = base64_encode($fileContent);
                $mimeType = $uploadedFile->getMimeType();
                $dataUri = 'data:' . $mimeType . ';base64,' . $base64;
                $user->setAvatar($dataUri);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        $allReservations = $reservationRepository->findBy(
            ['locataire' => $user],
            ['dateDebut' => 'DESC']
        );

        $upcomingReservations = [];
        $pastReservations = [];
        $now = new \DateTime();

        foreach ($allReservations as $reservation) {
            if ($reservation->getStatut() === 'CANCELLED') {
                $pastReservations[] = $reservation;
                continue;
            }

            if ($reservation->getDateFin() === null || $reservation->getDateFin() >= $now) {
                $upcomingReservations[] = $reservation;
            } else {
                $pastReservations[] = $reservation;
            }
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'averageRating' => $averageRating,
            'nbNotes' => $countNotes,
            'upcomingReservations' => $upcomingReservations,
            'pastReservations' => $pastReservations,
        ]);
    }

    #[Route('/profil/like/remove/{id}', name: 'app_like_remove', methods: ['POST'])]
    public function removeLike(Announce $annonce, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('remove_like_' . $annonce->getId(), $request->request->get('_token'))) {
            $like = $entityManager->getRepository(UserLikes::class)->findOneBy([
                'utilisateur' => $user,
                'annonce' => $annonce
            ]);

            if ($like) {
                $entityManager->remove($like);
                $entityManager->flush();

                $this->addFlash('success', 'Annonce retirée de vos favoris.');
            }
        } else {
            $this->addFlash('danger', 'Action non autorisée (Token de sécurité invalide).');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/mes-favoris', name: 'app_favoris')]
    public function mesFavoris(): Response
    {
        $user = $this->getUser();

        return $this->render('profile/favoris.html.twig', [
            'user' => $user,
        ]);
    }
}
