<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $me = new User();
        $me->setEmail('moi@test.com');
        $me->setPrenom('Moi');
        $me->setNom('Test');
        $me->setRole(1);
        $password = $this->hasher->hashPassword($me, 'password');
        $me->setPassword($password);
        $manager->persist($me);
        $prenoms = ['Lucas', 'Emma', 'Louis', 'Jade', 'Gabriel', 'Louise', 'Léo', 'Alice', 'Raphaël', 'Chloé'];
        $noms = ['Martin', 'Bernard', 'Thomas', 'Petit', 'Robert', 'Richard', 'Durand', 'Dubois', 'Moreau', 'Laurent'];
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $prenom = $prenoms[array_rand($prenoms)];
            $nom = $noms[array_rand($noms)];

            $user->setPrenom($prenom);
            $user->setNom($nom);
            $user->setEmail(strtolower($prenom . '.' . $nom . $i . '@test.com'));
            $user->setRole(1);
            $user->setPassword($password);

            $manager->persist($user);
            if ($i < 5) {
                $me->addContact($user);
            }
        }

        $manager->flush();
    }
}
