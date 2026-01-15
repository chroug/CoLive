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

    public function _before(ControllerTester $I): void
    {
        // 1. On crée l'utilisateur principal (Moi)
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
    public function testAccessDeniedForAnonymous(ControllerTester $I): void
    {
        $I->amOnPage('/message');
        $I->seeCurrentUrlEquals('/login');
    }
}
