<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    public function view(User $user, Video $video): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    public function update(User $user, Video $video): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    public function delete(User $user, Video $video): bool
    {
        return $user->role === UserRole::Admin->value;
    }
}
