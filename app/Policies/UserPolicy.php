<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->role === UserRole::Admin->value;
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->role === UserRole::Admin->value;
    }

    public function create(User $actor): bool
    {
        return $actor->role === UserRole::Admin->value;
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->role === UserRole::Admin->value;
    }

    /**
     * Block self-deletion and last-admin deletion.
     */
    public function delete(User $actor, User $target): bool
    {
        if (! ($actor->role === UserRole::Admin->value)) {
            return false;
        }

        // Cannot delete yourself
        if ($actor->id === $target->id) {
            return false;
        }

        // Cannot delete the last admin
        if ($target->role === UserRole::Admin->value) {
            $adminCount = User::where('role', UserRole::Admin->value)->count();
            if ($adminCount <= 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(User $actor, User $target): bool
    {
        return $actor->role === UserRole::Admin->value;
    }

    /**
     * Force-delete (permanent, bypasses soft delete).
     */
    public function forceDelete(User $actor, User $target): bool
    {
        return $actor->role === UserRole::Admin->value
            && $actor->id !== $target->id;
    }
}
