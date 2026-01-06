<?php

namespace App\Factory;

use App\Entity\UserLikes;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class LikeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UserLikes::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'utilisateur' => UserFactory::new(),
            'annonce' => AnnounceFactory::new(),
        ];
    }
}
