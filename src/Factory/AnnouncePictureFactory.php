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
            'contenu' => $this->getRandomImageBase64(),
            'annonce' => AnnounceFactory::new(),
            'dateCreation' => self::faker()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    private function getRandomImageBase64(): string
    {
        $url = 'https://loremflickr.com/400/300/interiors,room?lock=' . self::faker()->numberBetween(1, 1000);
        try {
            $imageContent = file_get_contents($url);
            if ($imageContent === false) {
                throw new \Exception("Erreur de téléchargement");
            }
            $base64 = base64_encode($imageContent);
            return 'data:image/jpeg;base64,' . $base64;

        } catch (\Exception $e) {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
        }
    }
}
