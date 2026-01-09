<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BotController extends AbstractController
{
    #[Route('/bot', name: 'app_bot')]
    public function index(): Response
    {
        return $this->render('bot/index.html.twig');
    }

    #[Route('/bot/ask', name: 'app_bot_ask', methods: ['POST'])]
    public function ask(Request $request, HttpClientInterface $client): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userQuestion = $data['message'] ?? '';

        $systemPrompt = "Tu es l'assistant virtuel de CoLive (site de colocation étudiante).
        Ton ton est : Jeune, serviable et concis.

        Ta base de données :
        - Prix : Inscription 100% gratuite.
        - Recherche : Barre de recherche permettant de choisir la ville / durée ou date / type
        - Annonces : Cliquer sur 'Proposer mon logement'.
        - Contact : support@colive.com.
        - Concept : Mise en relation étudiants / propriétaires.
        - Messagerie : Pour y accéder, il y a un bouton dans la barre du haut + il faut être connecté
        - Annonces : possibilité de les mettre en favoris en cliquant sur le coeur rouge. Une fois en favoris, les annonces sont visibles depuis le profil

        Si la question est hors sujet, dis poliment que tu ne sais pas.";

        $botResponse = "Une erreur est survenue.";

        try {
            $response = $client->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['MISTRAL_API_KEY'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'mistral-tiny',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userQuestion]
                    ],
                    'temperature' => 0.7,
                ],
            ]);

            $content = $response->toArray();
            $botResponse = $content['choices'][0]['message']['content'];

        } catch (\Exception $e) {
            $botResponse = "Désolé, je n'arrive pas à réfléchir pour le moment (Erreur API).";
        }

        return $this->json(['response' => $botResponse]);
    }
}
