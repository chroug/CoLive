<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Annonce;
use App\Entity\Liker;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

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
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'averageRating' => $averageRating,
            'nbNotes' => $countNotes,
        ]);
    }

    #[Route('/profil/like/remove/{id}', name: 'app_like_remove')]
    public function removeLike(Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $like = $entityManager->getRepository(Liker::class)->findOneBy([
            'utilisateur' => $user,
            'annonce' => $annonce
        ]);

        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce retirée de vos favoris.');
        }

        return $this->redirectToRoute('app_profile');
    }
}


