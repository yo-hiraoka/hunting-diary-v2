<?php

namespace Tests\Feature;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_download_diary_pdf(): void
    {
        $user = User::factory()->create();

        $diary = Diary::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.pdf', $diary));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/pdf'
        );
    }

    public function test_user_can_download_filtered_list_pdf(): void
    {
        $user = User::factory()->create();

        Diary::factory()->for($user)->create([
            'location' => '北山',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('diaries.pdf.list', [
                'location' => '北山',
            ]));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/pdf'
        );
    }
}
