<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenMeteoWeatherService
{
    /**
     * @return array{
     *     weather: string,
     *     sunrise_time: string,
     *     sunset_time: string
     * }
     */
    public function getDailyWeather(
        float $latitude,
        float $longitude,
        string $date
    ): array {
        $activityDate = Carbon::parse($date)->startOfDay();

        /*
         * 直近5日間と未来は予報API、
         * それより前は過去天気APIを使います。
         */
        $endpoint = $activityDate->gte(
            now()->startOfDay()->subDays(5)
        )
            ? 'https://api.open-meteo.com/v1/forecast'
            : 'https://archive-api.open-meteo.com/v1/archive';

        $response = Http::timeout(15)
            ->retry(2, 300)
            ->get($endpoint, [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'start_date' => $activityDate->toDateString(),
                'end_date' => $activityDate->toDateString(),
                'daily' => [
                    'weather_code',
                    'sunrise',
                    'sunset',
                ],
                'timezone' => 'Asia/Tokyo',
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                '天気情報サービスからデータを取得できませんでした。'
            );
        }

        $weatherCode = $response->json('daily.weather_code.0');
        $sunrise = $response->json('daily.sunrise.0');
        $sunset = $response->json('daily.sunset.0');

        if (
            $weatherCode === null ||
            $sunrise === null ||
            $sunset === null
        ) {
            throw new RuntimeException(
                '指定日の天気情報が見つかりませんでした。'
            );
        }

        return [
            'weather' => $this->weatherLabel(
                (int) $weatherCode
            ),
            'sunrise_time' => Carbon::parse($sunrise)
                ->format('H:i'),
            'sunset_time' => Carbon::parse($sunset)
                ->format('H:i'),
        ];
    }

    private function weatherLabel(int $code): string
    {
        return match (true) {
            $code === 0 => '快晴',
            in_array($code, [1, 2], true) => '晴れ',
            $code === 3 => '曇り',
            in_array($code, [45, 48], true) => '霧',
            in_array($code, [51, 53, 55], true) => '霧雨',
            in_array($code, [56, 57], true) => '着氷性の霧雨',
            in_array($code, [61, 63, 65], true) => '雨',
            in_array($code, [66, 67], true) => '着氷性の雨',
            in_array($code, [71, 73, 75, 77], true) => '雪',
            in_array($code, [80, 81, 82], true) => 'にわか雨',
            in_array($code, [85, 86], true) => 'にわか雪',
            in_array($code, [95, 96, 99], true) => '雷雨',
            default => '不明',
        };
    }
}
