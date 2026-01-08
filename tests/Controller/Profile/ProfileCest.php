<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Entity\User;
use App\Tests\Support\ControllerTester;

final class ProfileCest
{
    public function _before(ControllerTester $I): void
    {
        $user = $I->grabEntityFromRepository(User::class, ['email' => 'test@example.com']);
        $I->amLoggedInAs($user);
    }

    public function testUpdateProfile(ControllerTester $I): void
    {
        $I->amOnPage('/profil');

        $I->fillField('form[prenom]', 'Jean-Michel');
        $I->fillField('form[nom]', 'Dupont');
        $I->fillField('form[tel]', '0612345678');

        $I->click('Enregistrer');

        $I->see('Jean-Michel', '.user-name');
    }
}
