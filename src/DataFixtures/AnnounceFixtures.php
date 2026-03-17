<?php

namespace App\DataFixtures;

use App\Factory\AnnounceFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AnnounceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        AnnounceFactory::createMany(20, function() {
            return ['utilisateur' => UserFactory::random(),
            'isValidated' => true];
        });
    }

    public function getDependencies(): array { return [UserFixtures::class]; }
}
