<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('is-admin',     fn (User $user) => $user->role === 'admin');
        Gate::define('is-puskesmas', fn (User $user) => $user->role === 'puskesmas');
        Gate::define('is-kader',     fn (User $user) => $user->role === 'kader');
        Gate::define('is-user',      fn (User $user) => $user->role === 'user');
    }
}
