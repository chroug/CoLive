<?php

declare(strict_types=1);

namespace App\Tests\Controller\Home;

use App\Tests\Support\ControllerTester;

final class HomeCest
{
    public function _before(ControllerTester $I): void
    {
        // Code here will be executed before each test function.
    }

    // All `public` methods will be executed as tests.
    public function testHomePage(ControllerTester $I): void
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('La cohabitation pensée pour les étudiants en alternance', 'h1');
        $I->see('Se connecter', '.login-btn');
    }
}
