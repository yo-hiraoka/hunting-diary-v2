<?php

namespace Tests\Feature;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_own_diary(): void
    {
        $owner = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();

        $response = $this
            ->actingAs($owner)
            ->get(route('diaries.show', $diary));

        $response->assertOk();
        $response->assertSeeText('北山');
    }

    public function test_other_user_cannot_view_diary(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();

        $this
            ->actingAs($otherUser)
            ->get(route('diaries.show', $diary))
            ->assertForbidden();
    }

    public function test_other_user_cannot_delete_diary(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();

        $this
            ->actingAs($otherUser)
            ->delete(route('diaries.destroy', $diary))
            ->assertForbidden();

        $this->assertDatabaseHas('diaries', [
            'id' => $diary->id,
            'deleted_at' => null,
        ]);
    }

    public function test_owner_can_move_diary_to_trash(): void
    {
        $owner = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();

        $this
            ->actingAs($owner)
            ->delete(route('diaries.destroy', $diary))
            ->assertRedirect(route('diaries.index'));

        $this->assertSoftDeleted('diaries', [
            'id' => $diary->id,
        ]);
    }

    public function test_owner_can_restore_diary(): void
    {
        $owner = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();
        $diary->delete();

        $this
            ->actingAs($owner)
            ->patch(route('diaries.restore', $diary))
            ->assertRedirect(route('diaries.trash'));

        $this->assertNotSoftDeleted('diaries', [
            'id' => $diary->id,
        ]);
    }

    public function test_owner_can_permanently_delete_diary(): void
    {
        $owner = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();
        $diary->delete();

        $this
            ->actingAs($owner)
            ->delete(route('diaries.force-delete', $diary))
            ->assertRedirect(route('diaries.trash'));

        $this->assertDatabaseMissing('diaries', [
            'id' => $diary->id,
        ]);
    }

    public function test_other_user_cannot_download_diary_pdf(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $diary = Diary::factory()->for($owner)->create();

        $this
            ->actingAs($otherUser)
            ->get(route('diaries.pdf', $diary))
            ->assertForbidden();
    }
}
