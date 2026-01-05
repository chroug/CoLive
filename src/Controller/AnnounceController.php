<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Form\AnnounceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AnnounceController extends AbstractController
{
    #[Route('/announce', name: 'app_annouce')]
    public function index()
    {
        return $this->render('announce/index.html.twig');
    }
    #[Route('/announces/create', name: 'app_announces_create')]
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
}
