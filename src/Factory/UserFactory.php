<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class UserFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'prenom' => self::faker()->firstName(),
            'nom' => self::faker()->lastName(),
            'tel' => self::faker()->phoneNumber(),
            'password' => 'password',
            'role' => 1,
            'ville' => self::faker()->randomElement(['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Lille', 'Toulouse', 'Nantes']),
            'dateCreationCompte' => self::faker()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function(User $user) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        });
    }
}
