<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnnonceController extends AbstractController
{
    #[Route('/announce/create', name: 'app_announce_create')]
    public function index(Request $request, EntityManagerInterface $em): Response
    #[Route('/annonce', name: 'app_annonce')]
    public function index(): Response
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
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
            'formAnnonce' => $form->createView(),
        ]);
    }
}
