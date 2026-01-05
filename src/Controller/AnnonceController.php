<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AnnonceController extends AbstractController
{
    #[Route('/announces/create', name: 'app_announces_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUtilisateur($this->getUser());
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Votre annonce a été publiée avec succès. Elle est désormais visible par les utilisateurs.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonce/create.html.twig', [
            'controller_name' => 'AnnonceController',
            'formAnnonce' => $form->createView(),
        ]);
    }
}
