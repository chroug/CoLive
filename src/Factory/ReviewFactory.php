<?php

namespace App\Factory;

use App\Entity\Review;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ReviewFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Review::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'note' => self::faker()->numberBetween(3, 5),
            'commentaire' => self::faker()->realText(150),
            'utilisateur' => UserFactory::new(),
            'annonce' => AnnounceFactory::new(),
        ];
    }
}
