<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Inject Security Headers globally for all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Middleware aliases
        $middleware->alias([
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
            'no-cache' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // ─── Monthly Plant Growth Reset ─────────────────────────────────────
        // Runs on the 1st of every month at 00:05 WIB (Asia/Jakarta = UTC+7).
        // Laravel converts this to UTC for the cron expression: '5 17 31 * *'
        // which correctly fires at 00:05 WIB on the 1st. This is expected behaviour.
        // Resets total_leaves to 0 while preserving total_points (lifetime).
        $schedule->command('garda:reset-monthly-points')
            ->monthlyOn(1, '00:05')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->runInBackground()
            ->emailOutputOnFailure(env('ADMIN_NOTIFY_EMAIL', null));

        // ─── Prune expired cache entries ─────────────────────────────────────
        $schedule->command('cache:prune-stale-tags')
            ->hourly()
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
        \App\Console\Commands\CreateAdminCommand::class,
        \App\Console\Commands\ResetMonthlyPoints::class,
    ])
    ->create();