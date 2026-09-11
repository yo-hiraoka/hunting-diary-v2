<?php

namespace Tests\Feature;

use App\Enums\DiaryType;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiarySearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_own_diaries(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Diary::factory()->for($user)->create([
            'location' => '自分の猟場',
        ]);

        Diary::factory()->for($otherUser)->create([
            'location' => '他人の猟場',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.index'));

        $response->assertOk();
        $response->assertSeeText('自分の猟場');
        $response->assertDontSeeText('他人の猟場');
    }

    public function test_it_filters_by_diary_type(): void
    {
        $user = User::factory()->create();

        Diary::factory()->for($user)->create([
            'diary_type' => DiaryType::Hunting,
            'location' => '狩猟の場所',
        ]);

        Diary::factory()->for($user)->control()->create([
            'location' => '有害駆除の場所',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.index', [
                'diary_type' => 'control',
            ]));

        $response->assertOk();
        $response->assertSeeText('有害駆除の場所');
        $response->assertDontSeeText('狩猟の場所');
    }

    public function test_it_filters_by_location(): void
    {
        $user = User::factory()->create();

        Diary::factory()->for($user)->create([
            'location' => '北山地区',
        ]);

        Diary::factory()->for($user)->create([
            'location' => '南山地区',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.index', [
                'location' => '北山',
            ]));

        $response->assertOk();
        $response->assertSeeText('北山地区');
        $response->assertDontSeeText('南山地区');
    }

    public function test_it_filters_by_capture_status(): void
    {
        $user = User::factory()->create();

        Diary::factory()->for($user)->captured()->create([
            'location' => '捕獲あり',
        ]);

        Diary::factory()->for($user)->create([
            'location' => '捕獲なし',
            'has_capture' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.index', [
                'has_capture' => '1',
            ]));

        $response->assertOk();
        $response->assertSeeText('捕獲あり');
        $response->assertDontSeeText('捕獲なし');
    }

    public function test_it_filters_json_values(): void
    {
        $user = User::factory()->create();

        Diary::factory()->for($user)->create([
            'location' => '銃猟の場所',
            'hunting_methods' => ['gun'],
        ]);

        Diary::factory()->for($user)->create([
            'location' => '罠猟の場所',
            'hunting_methods' => ['trap'],
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.index', [
                'hunting_method' => 'trap',
            ]));

        $response->assertOk();
        $response->assertSeeText('罠猟の場所');
        $response->assertDontSeeText('銃猟の場所');
    }
}
