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
        $cityCoords = [
            'Paris'     => ['lat' => 48.8566, 'lon' => 2.3522],
            'Lyon'      => ['lat' => 45.7640, 'lon' => 4.8357],
            'Marseille' => ['lat' => 43.2965, 'lon' => 5.3698],
            'Bordeaux'  => ['lat' => 44.8378, 'lon' => -0.5792],
            'Lille'     => ['lat' => 50.6292, 'lon' => 3.0573],
            'Toulouse'  => ['lat' => 43.6047, 'lon' => 1.4442],
            'Nantes'    => ['lat' => 47.2184, 'lon' => -1.5536],
        ];

        $villeName = self::faker()->randomElement(array_keys($cityCoords));

        $lat = $cityCoords[$villeName]['lat'] + self::faker()->randomFloat(4, -0.05, 0.05);
        $lon = $cityCoords[$villeName]['lon'] + self::faker()->randomFloat(4, -0.05, 0.05);

        return [
            'titre' => self::faker()->sentence(4),
            'description' => self::faker()->paragraphs(2, true),
            'type' => self::faker()->randomElement(['Studio', 'Chambre', 'Appartement', 'Colocation']),
            'nb_pieces' => self::faker()->numberBetween(1, 4),
            'prix' => self::faker()->randomFloat(2, 350, 950),
            'ville' => $villeName,
            'latitude' => $lat,
            'longitude' => $lon,
            'adresse' => self::faker()->streetAddress(),
            'code_postal' => self::faker()->postcode(),
            'surface' => self::faker()->randomFloat(1, 12, 60),
            'disponibilite_debut' => self::faker()->dateTimeBetween('now', '+1 month'),
            'disponibilite_fin' => self::faker()->dateTimeBetween('+6 months', '+1 year'),
            'utilisateur' => UserFactory::new(),
            'isValidated' => self::faker()->boolean(),
        ];
    }
}
