<?php

declare(strict_types=1);

namespace App\Tests\Controller\Registration;

use App\Entity\User;
use App\Tests\Support\ControllerTester;

final class RegistrationCest
{
    public function _before(ControllerTester $I): void
    {
        // Code here will be executed before each test function.
    }

    // All `public` methods will be executed as tests.
    public function tryToTest(ControllerTester $I): void
    {
        // Write your test content here.
    }

    public function testSuccessfulRegistration(ControllerTester $I): void
    {
        $I->amOnPage('/registration');

        $I->fillField('registrationForm[prenom]', 'Amina');
        $I->fillField('registrationForm[nom]', 'Test');
        $I->fillField('registrationForm[email]', 'amina@example.com');
        $I->fillField('registrationForm[plainPassword][first]', 'Password123!');
        $I->fillField('registrationForm[plainPassword][second]', 'Password123!');

        $I->click('Créer mon compte');

        $I->seeCurrentUrlEquals('/');

        $I->seeInRepository(User::class, [
            'email' => 'amina@example.com',
            'prenom' => 'Amina'
        ]);
    }

    /**
     * Teste l'erreur quand les mots de passe ne sont pas identiques
     */
    public function testPasswordMismatchError(ControllerTester $I): void
    {
        $I->amOnPage('/registration');

        $I->fillField('registrationForm[prenom]', 'Jean');
        $I->fillField('registrationForm[nom]', 'Dupont');
        $I->fillField('registrationForm[email]', 'jean@example.com');
        $I->fillField('registrationForm[plainPassword][first]', 'Password123!');
        $I->fillField('registrationForm[plainPassword][second]', 'DifferentPassword456!');

        $I->click('Créer mon compte');

        $I->seeCurrentUrlEquals('/registration');
    }

    /**
     * Teste l'erreur si un champ est manquant (Logique du controller)
     */
    public function testEmptyFieldsError(ControllerTester $I): void
    {
        $I->amOnPage('/registration');
        $I->fillField('registrationForm[prenom]', 'Incomplet');
        $I->click('Créer mon compte');
    }
}
