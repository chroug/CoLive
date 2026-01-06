<?php

namespace App\DataFixtures;

use App\Factory\AnnouncePictureFactory;
use App\Factory\AnnounceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AnnouncePictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (AnnounceFactory::all() as $announce) {
            AnnouncePictureFactory::createMany(rand(2, 4), ['annonce' => $announce]);
        }
    }

    public function getDependencies(): array { return [AnnounceFixtures::class]; }
}
