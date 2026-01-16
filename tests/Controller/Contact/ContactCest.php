<?php

declare(strict_types=1);

namespace App\Tests\Controller\Contact;

use App\Entity\User;
use App\Tests\Support\ControllerTester;

final class ContactCest
{
    private int $myId;
    private int $otherUserId;

    public function _before(ControllerTester $I): void
    {
        $this->myId = $I->haveInRepository(User::class, [
            'prenom' => 'Moi',
            'nom' => 'Contact',
            'email' => 'moi.contact@test.com',
            'password' => 'password',
            'role' => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);

        $this->otherUserId = $I->haveInRepository(User::class, [
            'prenom' => 'Futur',
            'nom' => 'Ami',
            'email' => 'futur.ami@test.com',
            'password' => 'password',
            'role' => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);
    }
    /*
     * Verify if the contact appear in the list after the user clicked on the contact button
     */
    public function testAddContact(ControllerTester $I): void
    {
        $user = $I->grabEntityFromRepository(User::class, ['id' => $this->myId]);
        $I->amLoggedInAs($user);
        $I->amOnPage('/contact/ajouter/' . $this->otherUserId);
        $I->seeCurrentUrlEquals('/message/' . $this->otherUserId);
        $I->amOnPage('/message');
        $I->see('Futur Ami', '.contact-card .name');
    }
}
