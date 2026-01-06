<?php

namespace App\Factory;

use App\Entity\AnnouncePicture;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class AnnouncePictureFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return AnnouncePicture::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'url' => 'https://loremflickr.com/800/600/room?lock=' . self::faker()->unique()->numberBetween(1, 1000),
            'annonce' => AnnounceFactory::new(),
        ];
    }
}
