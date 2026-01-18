<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Entity\Announce;
use App\Entity\User;
use App\Tests\Support\ControllerTester;

final class AdminCest
{
    private User $admin;
    private User $student;

    public function _before(ControllerTester $I): void
    {
        $em = $I->grabService('doctrine.orm.entity_manager');

        $this->admin = new User();
        $this->admin->setNom('Admin');
        $this->admin->setPrenom('CoLive');
        $this->admin->setEmail('admin@test.com');
        $this->admin->setRole(2);
        $this->admin->setPassword('$2y$13$MbCO...');
        $em->persist($this->admin);

        $this->student = new User();
        $this->student->setNom('Etudiant');
        $this->student->setPrenom('Lambda');
        $this->student->setEmail('student@test.com');
        $this->student->setRole(1);
        $this->student->setPassword('$2y$13$MbCO...');
        $em->persist($this->student);

        $annonce = new Announce();
        $annonce->setTitre('Logement à modérer');
        $annonce->setDescription('Description de test');
        $annonce->setType('Chambre');
        $annonce->setPrix(400);
        $annonce->setVille('Paris');
        $annonce->setAdresse('12 rue de la Paix');
        $annonce->setNbPieces(1);
        $annonce->setLatitude(48.85);
        $annonce->setLongitude(2.35);
        $annonce->setUtilisateur($this->student);
        $annonce->setIsValidated(false);
        $em->persist($annonce);

        $em->flush();
    }

    public function tryAccessDashboardAsUser(ControllerTester $I): void
    {
        $I->amLoggedInAs($this->student);
        $I->amOnPage('/admin/dashboard');

        $I->seeResponseCodeIs(403);
    }

    public function tryToValidateAnnonceAsAdmin(ControllerTester $I): void
    {
        $I->amLoggedInAs($this->admin);
        $I->amOnPage('/admin/dashboard');

        $I->see('Logement à modérer');
        $I->see('Valider');

        $I->click('Valider');

        $I->seeCurrentUrlEquals('/admin/dashboard');

        $em = $I->grabService('doctrine.orm.entity_manager');
        $annonceRepository = $em->getRepository(Announce::class);
        $annonce = $annonceRepository->findOneBy(['titre' => 'Logement à modérer']);

        if (!$annonce) {
            throw new \Exception("ECHEC : L'annonce a disparu de la base !");
        }

        if ($annonce->isValidated() !== true) {
            throw new \Exception("ECHEC : L'admin a cliqué sur valider mais isValidated est toujours à false.");
        }
    }

    public function tryToRejectAnnonceAsAdmin(ControllerTester $I): void
    {
        $I->amLoggedInAs($this->admin);
        $I->amOnPage('/admin/dashboard');

        $I->click('Refuser');

        $em = $I->grabService('doctrine.orm.entity_manager');
        $annonceRepository = $em->getRepository(Announce::class);
        $annonce = $annonceRepository->findOneBy(['titre' => 'Logement à modérer']);

        if ($annonce) {
            throw new \Exception("ECHEC : L'annonce aurait dû être supprimée de la base de données.");
        }
    }
}
