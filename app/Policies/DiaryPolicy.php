<?php

namespace App\Policies;

use App\Models\Diary;
use App\Models\User;

class DiaryPolicy
{
    public function view(
        User $user,
        Diary $diary
    ): bool {
        return $user->id === $diary->user_id;
    }

    public function update(
        User $user,
        Diary $diary
    ): bool {
        return $user->id === $diary->user_id;
    }

    public function delete(
        User $user,
        Diary $diary
    ): bool {
        return $user->id === $diary->user_id;
    }

    public function restore(
        User $user,
        Diary $diary
    ): bool {
        return $user->id === $diary->user_id;
    }

    public function forceDelete(
        User $user,
        Diary $diary
    ): bool {
        return $user->id === $diary->user_id;
    }
}
