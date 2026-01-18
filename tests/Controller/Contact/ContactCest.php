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
    /*
     * Verify if the user is correctly deleted when the bin is clicked
     */
    public function testRemoveContact(ControllerTester $I): void
    {
        $me = $I->grabEntityFromRepository(User::class, ['id' => $this->myId]);
        $other = $I->grabEntityFromRepository(User::class, ['id' => $this->otherUserId]);
        $em = $I->grabService('doctrine.orm.entity_manager');
        $meManaged = $em->getRepository(User::class)->find($this->myId);
        $otherManaged = $em->getRepository(User::class)->find($this->otherUserId);
        $meManaged->addContact($otherManaged);
        $otherManaged->addContact($meManaged);
        $em->flush();
        $I->amLoggedInAs($me);
        $I->amOnPage('/message');
        $I->see('Futur Ami');
        $I->amOnPage('/contact/supprimer/' . $this->otherUserId);
        $I->seeCurrentUrlEquals('/message');
        $I->dontSee('Futur Ami', '.contact-card .name');
    }
}
