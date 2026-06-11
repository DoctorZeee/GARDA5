<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\HealthLog;
use App\Models\User;

class HealthLogPolicy
{
    /**
     * Admins and puskesmas can view all logs.
     * Kader can view logs from their own wilayah.
     * Regular users can only view their own logs.
     */
    public function viewAny(User $actor): bool
    {
        return in_array($actor->role, [
            UserRole::Admin->value,
            UserRole::Puskesmas->value,
            UserRole::Kader->value,
        ]);
    }

    public function view(User $actor, HealthLog $log): bool
    {
        if ($actor->role === UserRole::Admin->value || $actor->role === UserRole::Puskesmas->value) {
            return true;
        }

        if ($actor->role === UserRole::Kader->value) {
            // Kader can only see logs for users in their own wilayah
            return $log->user?->wilayah_id === $actor->wilayah_id;
        }

        return $log->user_id === $actor->id;
    }

    public function create(User $actor): bool
    {
        return $actor->role === UserRole::User->value;
    }

    public function delete(User $actor, HealthLog $log): bool
    {
        return $actor->role === UserRole::Admin->value;
    }
}
