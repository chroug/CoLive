<?php

declare(strict_types=1);

namespace App\Tests\Controller\Host_reviews;

use App\Tests\Support\ControllerTester;
use App\Entity\User;
use App\Entity\Announce;
use App\Entity\Review;

final class Host_reviewsCest
{
    private int $hostId;

    /**
     * On prépare les données avant chaque test de ce fichier
     */
    public function _before(ControllerTester $I): void
    {
        // 1. Créer l'hôte (celui qui reçoit les avis)
        $this->hostId = $I->haveInRepository(User::class, [
            'prenom'   => 'Marc',
            'nom'      => 'Lafont',
            'email'    => 'marc.host@example.com',
            'password' => 'password123',
            'role'     => 1,
            'dateCreationCompte' => new \DateTime('-1 month'),
        ]);

        // 2. Créer un client (celui qui écrit l'avis)
        $reviewerId = $I->haveInRepository(User::class, [
            'prenom'   => 'Julie',
            'nom'      => 'Reviewer',
            'email'    => 'julie@client.com',
            'password' => 'password123',
            'role'     => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);

        // 3. Récupérer les objets entités pour les lier
        $host = $I->grabEntityFromRepository(User::class, ['id' => $this->hostId]);
        $reviewer = $I->grabEntityFromRepository(User::class, ['id' => $reviewerId]);

        // 4. Créer une annonce pour cet hôte (car les avis sont liés aux annonces)
        // Note : Assure-toi que les champs obligatoires d'Announce sont bien là
        $announceId = $I->haveInRepository(Announce::class, [
            'titre'       => 'Chambre en colocation Lyon',
            'description' => 'Une superbe chambre pour étudiant en alternance.',
            'ville'       => 'Lyon',
            'adresse'     => '10 Rue de la Paix',
            'code_postal' => '69000',
            'prix'        => 450,
            'surface'     => 15,
            'type'        => 'Chambre',
            'utilisateur' => $host, // Relation ManyToOne vers User
        ]);

        $announce = $I->grabEntityFromRepository(Announce::class, ['id' => $announceId]);

        // 5. Créer l'avis (Review)
        $I->haveInRepository(Review::class, [
            'note'        => 5,
            'commentaire' => 'Marc est un hôte incroyable, je recommande !',
            'utilisateur' => $reviewer, // L'auteur de l'avis
            'annonce'     => $announce,  // L'annonce concernée
        ]);
    }

    /**
     * Teste l'affichage de la page d'avis d'un hôte
     */
    public function testSeeHostReviews(ControllerTester $I): void
    {
        $I->amOnPage('/hote/' . $this->hostId . '/avis');
        $I->seeResponseCodeIs(200);

        // Vérifie les informations de l'hôte dans la barre latérale (Sidebar)
        $I->see('Marc Lafont', '.profile-name');

        // Vérifie les stats (moyenne calculée par ton controller)
        $I->see('5.0', '.rating-large');
        $I->see('1 avis reçus', '.rating-count');

        // Vérifie l'avis dans la liste principale
        $I->see('Julie Reviewer', '.review-author');
        $I->see('Marc est un hôte incroyable, je recommande !', '.review-body');
    }

    /**
     * Teste le cas où l'hôte n'a pas encore reçu d'avis
     */
    public function testEmptyReviews(ControllerTester $I): void
    {
        // Créer un hôte tout neuf sans annonce ni avis
        $newHostId = $I->haveInRepository(User::class, [
            'prenom' => 'Paul',
            'nom' => 'Nouveau',
            'email' => 'paul@new.com',
            'password' => 'password',
            'role' => 1,
        ]);

        $I->amOnPage('/hote/' . $newHostId . '/avis');
        $I->seeResponseCodeIs(200);

        // Vérifie ton message vide défini dans le Twig
        $I->see("Aucun avis n'a été publié pour cet hôte.");
        $I->see('0.0', '.rating-large');
    }
}
