<?php

declare(strict_types=1);

namespace App\Tests\Controller\Announce;

use App\Entity\Announce;
use App\Entity\User;
use App\Tests\Support\ControllerTester;
use Doctrine\ORM\EntityManagerInterface;

final class AnnounceCest
{
    public function _before(ControllerTester $I): void
    {
        $em = $I->grabService('doctrine.orm.entity_manager');
        $user = new User();
        $user->setNom('Test');
        $user->setPrenom('Ilan');
        $user->setEmail('ilan@test.com');
        $user->setRole(1);
        $user->setPassword('$2y$13$MbCO...');
        $em->persist($user);
        $em->flush();
        $I->amLoggedInAs($user);
    }

    public function tryToCreateAnnonce(ControllerTester $I): void
    {
        $I->amOnPage('/announce/create');
        $I->submitForm('form', [
            'announce[titre]' => 'Announce Validée',
            'announce[description]' => 'Description testée avec le Twig minimal',
            'announce[type]' => 'Appartement',
            'announce[nb_pieces]' => 2,
            'announce[prix]' => 90,
            'announce[surface]' => 25,
            'announce[code_postal]' => '51100',
            'announce[ville]' => 'Reims',
            'announce[adresse]' => 'Rue de Vesle',
            'announce[disponibilite_debut]' => '2025-05-01',
            'announce[disponibilite_fin]' => '2025-05-30',
            'announce[latitude]' => 49.258,
            'announce[longitude]' => 4.031,
        ]);
        $I->dontSeeCurrentUrlEquals('/announce/create');
        $em = $I->grabService('doctrine.orm.entity_manager');
        $annonceRepository = $em->getRepository(Announce::class);
        $annonce = $annonceRepository->findOneBy(['titre' => 'Announce Validée']);
        if (!$annonce) {
            throw new \Exception("ECHEC : L'announce n'a pas été trouvée en base de données !");
        }
        if ($annonce->getVille() !== 'Reims') {
            throw new \Exception("ECHEC : La ville enregistrée n'est pas la bonne.");
        }
        if ($annonce->getCodePostal() !== '51100') {
            throw new \Exception("ECHEC : Le code postal n'a pas été enregistré correctement.");
        }
        if ($annonce->getSurface() != 25) {
            throw new \Exception("ECHEC : La surface n'a pas été enregistrée correctement.");
        }
    }
}
