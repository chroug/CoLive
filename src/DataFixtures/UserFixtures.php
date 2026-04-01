<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'admin@colive.com',
            'prenom' => 'Admin',
            'nom' => 'CoLive',
            'password' => 'admin',
            'role' => 2,
            'ville' => 'Paris'
        ]);

        UserFactory::createOne([
            'email' => 'test@example.com',
            'prenom' => 'Jean',
            'nom' => 'Test',
            'password' => 'password',
            'role' => 1,
            'ville' => 'Lyon'
        ]);

        UserFactory::createOne([
            'email' => 'bot@colive.com',
            'prenom' => 'Assistant',
            'nom' => 'FAQ',
            'password' => 'password',
            'role' => 1,
            'ville' => 'Paris'
        ]);

        UserFactory::createMany(15);
    }
}
