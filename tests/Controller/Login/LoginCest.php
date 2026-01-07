<?php

declare(strict_types=1);

namespace App\Tests\Controller\Login;

use App\Tests\Support\ControllerTester;

final class LoginCest
{
    public function testSuccessfulLogin(ControllerTester $I): void
    {
        $I->amOnPage('/login');

        $I->fillField('_username', 'test@example.com');
        $I->fillField('_password', 'password');
        $I->click('Se connecter');

        $I->seeCurrentUrlEquals('/');

        $I->amOnPage('/profil');
        $I->see('Se déconnecter', '.logout-btn');
    }

    public function testFailedLogin(ControllerTester $I): void
    {
        $I->amOnPage('/login');
        $I->fillField('_username', 'test@example.com');
        $I->fillField('_password', 'mauvais_mdp');
        $I->click('Se connecter');

        $I->seeCurrentUrlEquals('/login');
        $I->seeElement('.flash-error');
    }
}
