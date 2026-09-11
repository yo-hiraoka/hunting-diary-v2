<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this
            ->get('/')
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_to_diaries(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get('/')
            ->assertRedirect(route('diaries.index'));
    }
}
