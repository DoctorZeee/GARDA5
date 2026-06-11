<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin     = 'admin';
    case Puskesmas = 'puskesmas';
    case Kader     = 'kader';
    case User      = 'user';

    /**
     * Human-readable label (Bahasa Indonesia).
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin     => 'Administrator',
            self::Puskesmas => 'Puskesmas',
            self::Kader     => 'Kader',
            self::User      => 'Warga',
        };
    }

    /**
     * Named route for dashboard redirect after login.
     */
    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin     => 'admin.dashboard',
            self::Puskesmas => 'puskesmas.dashboard',
            self::Kader     => 'kader.dashboard',
            self::User      => 'user.dashboard',
        };
    }

    /**
     * All roles allowed to manage platform data (not just warga).
     */
    public static function staffRoles(): array
    {
        return [self::Admin, self::Puskesmas, self::Kader];
    }

    /**
     * Resolve from string safely (null on unknown).
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
