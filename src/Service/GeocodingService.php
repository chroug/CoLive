<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCoordinates(string $adresse, ?string $codePostal, string $ville): ?array
    {
        $query = sprintf('%s, %s %s, France', $adresse, $codePostal ?? '', $ville);

        try {
            $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1
                ],
                'headers' => [
                    'User-Agent' => 'ColiveApp/1.0 (contact@colive.fr)'
                ]
            ]);

            $data = $response->toArray();

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lon' => (float) $data[0]['lon'],
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
