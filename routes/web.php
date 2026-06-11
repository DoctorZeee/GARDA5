<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => view('landing'));

    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:5,1');

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])
        ->middleware('throttle:10,1');
});

// ─── Authenticated Routes ──────────────────────────────────────────────────────
Route::middleware(['auth', 'no-cache'])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout')
        ->middleware('throttle:10,1');   // Prevent logout-flood

    // ── Admin ──────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('/users/{id}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])
            ->name('users.restore');

        Route::resource('videos', \App\Http\Controllers\Admin\VideoController::class);
    });

    // ── Puskesmas ──────────────────────────────────────────────────────────
    Route::middleware('role:puskesmas')->prefix('puskesmas')->name('puskesmas.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Puskesmas\DashboardController::class, 'index'])
            ->name('dashboard');
    });

    // ── Kader ──────────────────────────────────────────────────────────────
    Route::middleware('role:kader')->prefix('kader')->name('kader.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Kader\DashboardController::class, 'index'])
            ->name('dashboard');
    });

    // ── User (Warga) ───────────────────────────────────────────────────────
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/health-logs', [\App\Http\Controllers\User\HealthLogController::class, 'store'])
            ->name('health-logs.store');

        Route::post('/checkin', [\App\Http\Controllers\User\RewardController::class, 'checkin'])
            ->name('checkin')
            ->middleware('throttle:10,1');

        Route::post('/video/{video}/claim', [\App\Http\Controllers\User\RewardController::class, 'claimVideo'])
            ->name('video.claim')
            ->middleware('throttle:20,1');
    });
});
