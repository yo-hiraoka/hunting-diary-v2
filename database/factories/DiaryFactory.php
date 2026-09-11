<?php

namespace Database\Factories;

use App\Enums\DiaryType;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diary>
 */
class DiaryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'fiscal_year' => 2026,
            'overall_number' => fake()->unique()->numberBetween(1, 999999),
            'diary_type' => DiaryType::Hunting,
            'type_number' => fake()->unique()->numberBetween(1, 999999),

            'activity_date' => '2026-06-01',
            'departure_time' => '08:00',
            'return_time' => '16:00',

            'weather' => '晴れ',
            'sunrise_time' => '04:30',
            'sunset_time' => '19:00',
            'weather_prefecture' => '静岡県',
            'weather_city' => '浜松市',
            'weather_latitude' => 34.7108344,
            'weather_longitude' => 137.7261258,
            'weather_fetched_at' => now(),

            'hunting_methods' => ['gun'],
            'activities' => ['patrol'],
            'transportations' => ['car'],

            'location' => '北山',

            'has_capture' => false,
            'capture_details' => null,
            'has_sighting' => true,
            'sighting_details' => 'シカ1頭',

            'has_gun' => true,
            'has_used_ammunition' => false,
            'sabot_count' => 0,
            'slug_count' => 0,
            'bs_count' => 0,
            'shot_count' => 0,

            'notes' => 'テスト用の日誌です。',
        ];
    }

    public function control(): static
    {
        return $this->state(fn (): array => [
            'diary_type' => DiaryType::Control,
        ]);
    }

    public function captured(): static
    {
        return $this->state(fn (): array => [
            'has_capture' => true,
            'capture_details' => 'イノシシ1頭',
            'activities' => ['capture'],
        ]);
    }
}
