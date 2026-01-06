<?php

namespace App\Factory;

use App\Entity\Like;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class LikeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Like::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'utilisateur' => UserFactory::new(),
            'annonce' => AnnounceFactory::new(),
        ];
    }
}
