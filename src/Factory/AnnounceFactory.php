<?php

namespace App\Factory;

use App\Entity\Announce;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class AnnounceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Announce::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'titre' => self::faker()->sentence(4),
            'description' => self::faker()->paragraphs(2, true),
            'type' => self::faker()->randomElement(['Studio', 'Chambre', 'Appartement', 'Colocation']),
            'nb_pieces' => self::faker()->numberBetween(1, 4),
            'prix' => self::faker()->randomFloat(2, 350, 950),
            'latitude' => self::faker()->latitude(45.7, 45.8),
            'longitude' => self::faker()->longitude(4.8, 4.9),
            'adresse' => self::faker()->streetAddress(),
            'ville' => 'Lyon',
            'code_postal' => self::faker()->postcode(),
            'surface' => self::faker()->randomFloat(1, 12, 60),
            'disponibilite_debut' => self::faker()->dateTimeBetween('now', '+1 month'),
            'disponibilite_fin' => self::faker()->dateTimeBetween('+6 months', '+1 year'),
            'utilisateur' => UserFactory::new(),
        ];
    }
}
