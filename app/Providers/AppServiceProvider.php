<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\HealthLog;
use App\Models\User;
use App\Models\Video;
use App\Policies\HealthLogPolicy;
use App\Policies\UserPolicy;
use App\Policies\VideoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ─── Policies ─────────────────────────────────────────────────────────

        Gate::policy(User::class,      UserPolicy::class);
        Gate::policy(Video::class,     VideoPolicy::class);
        Gate::policy(HealthLog::class, HealthLogPolicy::class);

        // ─── Gates (role shortcuts) ───────────────────────────────────────────

        Gate::define('is-admin',     fn (User $user) => $user->role === UserRole::Admin->value);
        Gate::define('is-puskesmas', fn (User $user) => $user->role === UserRole::Puskesmas->value);
        Gate::define('is-kader',     fn (User $user) => $user->role === UserRole::Kader->value);
        Gate::define('is-user',      fn (User $user) => $user->role === UserRole::User->value);
        Gate::define('is-staff',     fn (User $user) => in_array($user->role, [
            UserRole::Admin->value,
            UserRole::Puskesmas->value,
            UserRole::Kader->value,
        ]));

        // ─── Global Password Policy ───────────────────────────────────────────
        // Applies as the default when using Password::defaults() in Form Requests.

        Password::defaults(
            Password::min(8)
                ->letters()
                ->numbers()
                ->uncompromised()
        );
    }
}
