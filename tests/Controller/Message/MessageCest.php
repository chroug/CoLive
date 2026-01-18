<?php

declare(strict_types=1);

namespace App\Tests\Controller\Message;

use App\Entity\User;
use App\Entity\Message;
use App\Tests\Support\ControllerTester;

final class MessageCest
{
    private int $myId;
    private int $friendId;
    /*
     * Creation of 2 persons to test the messaging webpage
     */
    public function _before(ControllerTester $I): void
    {
        $this->myId = $I->haveInRepository(User::class, [
            'prenom' => 'Jean',
            'nom' => 'Messagerie',
            'email' => 'jean.msg@test.com',
            'password' => 'password',
            'role' => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);

        $this->friendId = $I->haveInRepository(User::class, [
            'prenom' => 'Paul',
            'nom' => 'Ami',
            'email' => 'paul.ami@test.com',
            'password' => 'password',
            'role' => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);
    }
    /*
     * Verify that the user can't use the messaging and that he is redirected on login webpage
     */
    public function testAccessDeniedForAnonymous(ControllerTester $I): void
    {
        $I->amOnPage('/message');
        $I->seeCurrentUrlEquals('/login');
    }
    /*
     * Verify if browser send back a code 200, if there is the sidebar and if the h2 is present
     */
    public function testPageLoadsForAuthenticatedUser(ControllerTester $I): void
    {
        $user = $I->grabEntityFromRepository(User::class, ['id' => $this->myId]);
        $I->amLoggedInAs($user);
        $I->amOnPage('/message');
        $I->seeResponseCodeIs(200);
        $I->see('Messagerie', 'h2');
        $I->seeElement('.messagerie-sidebar');
    }
    /*
     * Verify if the user can send a message and if the receiver can see the message / can respond
     */
    public function testSendMessage(ControllerTester $I): void
    {
        $user = $I->grabEntityFromRepository(User::class, ['id' => $this->myId]);
        $I->amLoggedInAs($user);
        $I->amOnPage('/message/' . $this->friendId);
        $I->seeResponseCodeIs(200);
        $I->see('Paul Ami');
        $I->fillField('content', 'Salut Paul, ceci est un test Codeception !');
        $I->click('Envoyer');
        $I->seeCurrentUrlEquals('/message/' . $this->friendId);
        $I->see('Salut Paul, ceci est un test Codeception !', '.message-bubble');
        $I->seeInRepository(Message::class, [
            'content' => 'Salut Paul, ceci est un test Codeception !',
            'sender' => $this->myId,
            'recipient' => $this->friendId
        ]);
    }
}
