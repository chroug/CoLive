<?php

namespace App\DataFixtures;

use App\Factory\ReviewFactory;
use App\Factory\AnnounceFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        ReviewFactory::createMany(40, function() {
            return [
                'utilisateur' => UserFactory::random(),
                'annonce' => AnnounceFactory::random()
            ];
        });
    }

    public function getDependencies(): array { return [UserFixtures::class, AnnounceFixtures::class]; }
}
