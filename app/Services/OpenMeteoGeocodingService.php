<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenMeteoGeocodingService
{
    /**
     * @return array{
     *     latitude: float,
     *     longitude: float,
     *     name: string,
     *     prefecture: string
     * }|null
     */
    public function findCoordinates(
        string $prefecture,
        string $city
    ): ?array {
        $response = Http::timeout(10)
            ->retry(2, 200)
            ->get(
                'https://geocoding-api.open-meteo.com/v1/search',
                [
                    'name' => $city,
                    'count' => 10,
                    'language' => 'ja',
                    'format' => 'json',
                    'countryCode' => 'JP',
                ]
            );

        if ($response->failed()) {
            return null;
        }

        $locations = $response->json('results', []);

        if (! is_array($locations)) {
            return null;
        }

        foreach ($locations as $location) {
            if (
                ! $this->matchesLocation(
                    $location,
                    $prefecture,
                    $city
                )
            ) {
                continue;
            }

            if (
                ! isset(
                    $location['latitude'],
                    $location['longitude']
                )
            ) {
                continue;
            }

            return [
                'latitude' => (float) $location['latitude'],
                'longitude' => (float) $location['longitude'],
                'name' => (string) $location['name'],
                'prefecture' => (string) $location['admin1'],
            ];
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $location
     */
    private function matchesLocation(
        array $location,
        string $prefecture,
        string $city
    ): bool {
        if (($location['country_code'] ?? null) !== 'JP') {
            return false;
        }

        $resultPrefecture = (string) ($location['admin1'] ?? '');
        $resultCity = (string) ($location['name'] ?? '');

        return $this->normalizePrefecture($resultPrefecture)
            === $this->normalizePrefecture($prefecture)
            && $this->normalizeCity($resultCity)
            === $this->normalizeCity($city);
    }

    private function normalizePrefecture(string $value): string
    {
        $value = $this->removeSpaces($value);

        return preg_replace('/(都|道|府|県)$/u', '', $value)
            ?? $value;
    }

    private function normalizeCity(string $value): string
    {
        $value = $this->removeSpaces($value);

        return preg_replace('/(市|区|町|村)$/u', '', $value)
            ?? $value;
    }

    private function removeSpaces(string $value): string
    {
        return str_replace(
            [' ', '　'],
            '',
            trim($value)
        );
    }
}
