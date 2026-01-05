<?php

declare(strict_types=1);

namespace App\Tests\Controller\home;

use App\Entity\Annonce;
use App\Entity\User;
use App\Tests\Support\ControllerTester;
use Doctrine\ORM\EntityManagerInterface;

final class HomeCest
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
            'annonce[titre]' => 'Annonce Validée',
            'annonce[description]' => 'Description testée avec le Twig minimal',
            'annonce[type]' => 'Appartement',
            'annonce[nb_pieces]' => 2,
            'annonce[prix]' => 90,
            'annonce[surface]' => 25,
            'annonce[code_postal]' => '51100',
            'annonce[ville]' => 'Reims',
            'annonce[adresse]' => 'Rue de Vesle',
            'annonce[disponibilite_debut]' => '2025-05-01',
            'annonce[disponibilite_fin]' => '2025-05-30',
            'annonce[latitude]' => 49.258,
            'annonce[longitude]' => 4.031,
        ]);
        $I->dontSeeCurrentUrlEquals('/announce/create');
        $em = $I->grabService('doctrine.orm.entity_manager');
        $annonceRepository = $em->getRepository(Annonce::class);
        $annonce = $annonceRepository->findOneBy(['titre' => 'Annonce Validée']);
        if (!$annonce) {
            throw new \Exception("ECHEC : L'annonce n'a pas été trouvée en base de données !");
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
