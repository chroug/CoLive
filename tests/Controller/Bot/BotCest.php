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
        $user = $I->grabEntityFromRepository(User::class, ['id' => $this->userId]);
        $I->amLoggedInAs($user);
    }

    /**
     * Verify if the page load correctly
     */
    public function testBotPageDisplay(ControllerTester $I): void
    {
        $I->amOnPage('/bot');
        $I->seeResponseCodeIs(200);
        $I->see('Assistant CoLive', 'h3');
        $I->see('En ligne (IA)', 'span');
        $I->seeElement('#userInput');
        $I->seeElement('#chatBox');
        $I->see("Bonjour ! Je suis l'IA de CoLive. Une question sur le site ?", '.message.bot .bubble');
    }
}
