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
        $this->hostId = $I->haveInRepository(User::class, [
            'prenom'   => 'Marc',
            'nom'      => 'Lafont',
            'email'    => 'marc.host@example.com',
            'password' => 'password123',
            'role'     => 1,
            'dateCreationCompte' => new \DateTime('-1 month'),
        ]);

        $reviewerId = $I->haveInRepository(User::class, [
            'prenom'   => 'Julie',
            'nom'      => 'Reviewer',
            'email'    => 'julie@client.com',
            'password' => 'password123',
            'role'     => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);

        $host = $I->grabEntityFromRepository(User::class, ['id' => $this->hostId]);
        $reviewer = $I->grabEntityFromRepository(User::class, ['id' => $reviewerId]);

        $announceId = $I->haveInRepository(Announce::class, [
            'titre'       => 'Chambre en colocation Lyon',
            'description' => 'Une superbe chambre pour étudiant en alternance.',
            'ville'       => 'Lyon',
            'adresse'     => '10 Rue de la Paix',
            'code_postal' => '69000',
            'prix'        => 450.0,
            'surface'     => 15.0,
            'type'        => 'Chambre',
            'nb_pieces'   => 2,
            'latitude'    => 45.764043,
            'longitude'   => 4.835659,
            'dateCreation' => new \DateTime(),
            'disponibilite_debut' => new \DateTime(),
            'disponibilite_fin'   => new \DateTime('+6 months'),
            'utilisateur' => $host,
        ]);

        $announce = $I->grabEntityFromRepository(Announce::class, ['id' => $announceId]);

        $I->haveInRepository(Review::class, [
            'note'        => 5,
            'commentaire' => 'Marc est un hôte incroyable, je recommande !',
            'dateCreation' => new \DateTime(),
            'utilisateur' => $reviewer,
            'annonce'     => $announce,
        ]);
    }

    /**
     * Teste l'affichage de la page d'avis d'un hôte
     */
    public function testSeeHostReviews(ControllerTester $I): void
    {
        $I->amOnPage('/hote/' . $this->hostId . '/avis');
        $I->seeResponseCodeIs(200);

        $I->see('Marc Lafont', '.profile-name');

        $I->see('5.0', '.rating-large');
        $I->see('1 avis reçus', '.rating-count');

        $I->see('Julie Reviewer', '.review-author');
        $I->see('Marc est un hôte incroyable, je recommande !', '.review-body');
    }

    /**
     * Teste le cas où l'hôte n'a pas encore reçu d'avis
     */
    public function testEmptyReviews(ControllerTester $I): void
    {
        $newHostId = $I->haveInRepository(User::class, [
            'prenom' => 'Paul',
            'nom' => 'Nouveau',
            'email' => 'paul' . rand(0, 999) . '@new.com',
            'password' => 'password',
            'role' => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);

        $I->amOnPage('/hote/' . $newHostId . '/avis');
        $I->seeResponseCodeIs(200);

        $I->see("Aucun avis n'a été publié pour cet hôte.");
        $I->see('0.0', '.rating-large');
    }
}
