<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function register(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        Security $security
    ): Response
    {
        if ($request->isMethod('POST')) {

            if ($this->getUser()) {
                return $this->redirectToRoute('app_home');
            }

            $data = $request->request->all('registrationForm');

            $prenom        = $data['prenom'];
            $nom           = $data['nom'];
            $email         = $data['email'];
            $firstPassword = $data['plainPassword']['first'];
            $secondPassword= $data['plainPassword']['second'];

            if (!$prenom || !$nom || !$email || !$firstPassword || !$secondPassword) {
                $this->addFlash('error', 'Tous les champs doivent être complétés.');
                return $this->redirectToRoute('app_registration');
            }

            $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé par un autre compte.');
                return $this->redirectToRoute('app_registration');
            }

            if ($firstPassword !== $secondPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_registration');
            }

            $user = new User();
            $user->setPrenom($prenom)
                ->setNom($nom)
                ->setEmail($email)
                ->setPassword($passwordHasher->hashPassword($user, $firstPassword));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès !');

            return $security->login($user, 'form_login', 'main');

        }

        return $this->render('registration/index.html.twig');
    }
}
