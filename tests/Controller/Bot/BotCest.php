<?php

declare(strict_types=1);

namespace App\Tests\Controller\Bot;

use App\Entity\User;
use App\Tests\Support\ControllerTester;

final class BotCest
{
    private int $userId;

    public function _before(ControllerTester $I): void
    {
        $this->userId = $I->haveInRepository(User::class, [
            'prenom' => 'Tester',
            'nom' => 'Bot',
            'email' => 'bot.tester@test.com',
            'password' => 'password',
            'role' => 1,
            'dateCreationCompte' => new \DateTime(),
        ]);
    }

    /**
     * Verify if the page load correctly if the user is connected
     */
    public function testBotPageDisplay(ControllerTester $I): void
    {
        $user = $I->grabEntityFromRepository(User::class, ['id' => $this->userId]);
        $I->amLoggedInAs($user);
        $I->amOnPage('/bot');
        $I->seeResponseCodeIs(200);
        $I->see('Assistant CoLive');
        $I->seeElement('#userInput');
    }

    /**
     * Verify if the url is redirected on the login page
     */
    public function testAccessDeniedForAnonymous(ControllerTester $I): void
    {
        $I->amOnPage('/bot');
        $I->dontSeeCurrentUrlEquals('/bot');
        $I->seeCurrentUrlEquals('/login');
    }
}
