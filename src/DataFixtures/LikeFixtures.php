<?php

namespace App\DataFixtures;

use App\Factory\LikeFactory;
use App\Factory\AnnounceFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        LikeFactory::createMany(60, function() {
            return [
                'utilisateur' => UserFactory::random(),
                'annonce' => AnnounceFactory::random()
            ];
        });
    }

    public function getDependencies(): array { return [UserFixtures::class, AnnounceFixtures::class]; }
}
