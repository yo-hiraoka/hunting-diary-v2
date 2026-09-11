<?php

namespace Tests\Feature;

use App\Enums\DiaryType;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryWriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_location_can_create_diary(): void
    {
        $user = $this->createUserWithLocation();

        $response = $this
            ->actingAs($user)
            ->post(
                route('diaries.store'),
                $this->validPayload()
            );

        $response->assertRedirect(route('diaries.index'));

        $diary = Diary::query()->firstOrFail();

        $this->assertSame($user->id, $diary->user_id);
        $this->assertSame(2026, $diary->fiscal_year);
        $this->assertSame(1, $diary->overall_number);
        $this->assertSame(1, $diary->type_number);
        $this->assertSame(
            DiaryType::Hunting,
            $diary->diary_type
        );
        $this->assertSame('北山地区', $diary->location);
    }

    public function test_user_without_location_is_redirected(): void
    {
        $user = User::factory()->create([
            'weather_prefecture' => null,
            'weather_city' => null,
            'weather_latitude' => null,
            'weather_longitude' => null,
        ]);

        $this
            ->actingAs($user)
            ->get(route('diaries.create'))
            ->assertRedirect(
                route('settings.location.edit')
            );
    }

    public function test_ammunition_requires_at_least_one_round(): void
    {
        $user = $this->createUserWithLocation();

        $payload = $this->validPayload([
            'has_gun' => '1',
            'has_used_ammunition' => '1',
            'sabot_count' => 0,
            'slug_count' => 0,
            'bs_count' => 0,
            'shot_count' => 0,
        ]);

        $this
            ->actingAs($user)
            ->post(route('diaries.store'), $payload)
            ->assertSessionHasErrors('ammunition_counts');

        $this->assertDatabaseCount('diaries', 0);
    }

    public function test_ammunition_cannot_be_used_without_gun(): void
    {
        $user = $this->createUserWithLocation();

        $payload = $this->validPayload([
            'has_gun' => '0',
            'has_used_ammunition' => '1',
            'slug_count' => 1,
        ]);

        $this
            ->actingAs($user)
            ->post(route('diaries.store'), $payload)
            ->assertSessionHasErrors(
                'has_used_ammunition'
            );

        $this->assertDatabaseCount('diaries', 0);
    }

    public function test_same_type_update_keeps_numbers(): void
    {
        $user = $this->createUserWithLocation();

        $this
            ->actingAs($user)
            ->post(
                route('diaries.store'),
                $this->validPayload()
            );

        $diary = Diary::query()->firstOrFail();

        $originalOverallNumber = $diary->overall_number;
        $originalTypeNumber = $diary->type_number;

        $payload = $this->validPayload([
            'location' => '変更後の場所',
        ]);

        $this
            ->actingAs($user)
            ->patch(
                route('diaries.update', $diary),
                $payload
            )
            ->assertRedirect(
                route('diaries.show', $diary)
            );

        $diary->refresh();

        $this->assertSame(
            $originalOverallNumber,
            $diary->overall_number
        );

        $this->assertSame(
            $originalTypeNumber,
            $diary->type_number
        );

        $this->assertSame(
            '変更後の場所',
            $diary->location
        );
    }

    public function test_type_change_issues_new_type_number(): void
    {
        $user = $this->createUserWithLocation();

        $this
            ->actingAs($user)
            ->post(
                route('diaries.store'),
                $this->validPayload()
            );

        $diary = Diary::query()->firstOrFail();
        $originalOverallNumber = $diary->overall_number;

        $payload = $this->validPayload([
            'diary_type' => 'control',
        ]);

        $this
            ->actingAs($user)
            ->patch(
                route('diaries.update', $diary),
                $payload
            )
            ->assertRedirect(
                route('diaries.show', $diary)
            );

        $diary->refresh();

        $this->assertSame(
            $originalOverallNumber,
            $diary->overall_number
        );

        $this->assertSame(
            DiaryType::Control,
            $diary->diary_type
        );

        $this->assertSame(1, $diary->type_number);
    }

    public function test_fiscal_year_change_issues_new_numbers(): void
    {
        $user = $this->createUserWithLocation();

        $this
            ->actingAs($user)
            ->post(
                route('diaries.store'),
                $this->validPayload([
                    'activity_date' => '2026-06-01',
                ])
            );

        $diary = Diary::query()->firstOrFail();

        $payload = $this->validPayload([
            'activity_date' => '2027-04-01',
        ]);

        $this
            ->actingAs($user)
            ->patch(
                route('diaries.update', $diary),
                $payload
            );

        $diary->refresh();

        $this->assertSame(2027, $diary->fiscal_year);
        $this->assertSame(1, $diary->overall_number);
        $this->assertSame(1, $diary->type_number);
    }

    public function test_other_user_cannot_update_diary(): void
    {
        $owner = $this->createUserWithLocation();
        $otherUser = $this->createUserWithLocation();

        $diary = Diary::factory()
            ->for($owner)
            ->create();

        $this
            ->actingAs($otherUser)
            ->patch(
                route('diaries.update', $diary),
                $this->validPayload()
            )
            ->assertForbidden();
    }

    private function createUserWithLocation(): User
    {
        return User::factory()->create([
            'weather_prefecture' => '静岡県',
            'weather_city' => '浜松市',
            'weather_latitude' => 34.7108344,
            'weather_longitude' => 137.7261258,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(
        array $overrides = []
    ): array {
        return array_merge([
            'diary_type' => 'hunting',
            'activity_date' => '2026-06-01',
            'departure_time' => '08:00',
            'return_time' => '16:00',

            'weather' => '晴れ',
            'sunrise_time' => '04:30',
            'sunset_time' => '19:00',
            'weather_was_fetched' => '0',

            'hunting_methods' => ['gun'],
            'activities' => ['patrol'],
            'transportations' => ['car'],

            'location' => '北山地区',

            'has_capture' => '0',
            'capture_details' => null,

            'has_sighting' => '1',
            'sighting_details' => 'シカ1頭',

            'has_gun' => '1',
            'has_used_ammunition' => '0',

            'sabot_count' => 0,
            'slug_count' => 0,
            'bs_count' => 0,
            'shot_count' => 0,

            'notes' => 'テスト用の日誌です。',
        ], $overrides);
    }

    public function test_ammunition_count_cannot_exceed_99(): void
    {
        $user = $this->createUserWithLocation();

        $payload = $this->validPayload([
            'has_gun' => '1',
            'has_used_ammunition' => '1',
            'sabot_count' => 100,
        ]);

        $this
            ->actingAs($user)
            ->post(route('diaries.store'), $payload)
            ->assertSessionHasErrors('sabot_count');

        $this->assertDatabaseCount('diaries', 0);
    }
}
