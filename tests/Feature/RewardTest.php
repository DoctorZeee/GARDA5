<?php

use App\Models\User;
use App\Models\UserPoint;
use App\Models\UserVideoClaim;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── CHECKIN ──────────────────────────────────────────────────────────────────

test('user dapat checkin dan mendapat poin', function () {
    $user = User::factory()->create(['role' => 'user']);
    UserPoint::factory()->create(['user_id' => $user->id, 'total_points' => 0, 'total_leaves' => 0]);

    $this->actingAs($user)
        ->post(route('user.checkin'))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(UserPoint::where('user_id', $user->id)->value('total_points'))->toBe(1);
});

test('user tidak bisa checkin dua kali sehari', function () {
    $user = User::factory()->create(['role' => 'user']);
    UserPoint::factory()->create([
        'user_id'           => $user->id,
        'total_points'      => 1,
        'last_checkin_date' => now()->toDateString(),
    ]);

    $this->actingAs($user)
        ->post(route('user.checkin'))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(UserPoint::where('user_id', $user->id)->value('total_points'))->toBe(1);
});

// ── CLAIM VIDEO ───────────────────────────────────────────────────────────────

test('user dapat klaim video dan mendapat poin', function () {
    $user  = User::factory()->create(['role' => 'user']);
    $video = Video::factory()->create(['points_reward' => 3, 'is_active' => true]);
    UserPoint::factory()->create(['user_id' => $user->id, 'total_points' => 0, 'total_leaves' => 0]);

    $this->actingAs($user)
        ->post(route('user.video.claim', $video))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(UserVideoClaim::where('user_id', $user->id)->where('video_id', $video->id)->exists())->toBeTrue();
    expect(UserPoint::where('user_id', $user->id)->value('total_points'))->toBe(3);
});

test('user tidak bisa klaim video yang sama dua kali', function () {
    $user  = User::factory()->create(['role' => 'user']);
    $video = Video::factory()->create(['points_reward' => 3, 'is_active' => true]);
    UserPoint::factory()->create(['user_id' => $user->id, 'total_points' => 3, 'total_leaves' => 3]);
    UserVideoClaim::create(['user_id' => $user->id, 'video_id' => $video->id]);

    $this->actingAs($user)
        ->post(route('user.video.claim', $video))
        ->assertRedirect()
        ->assertSessionHas('error');

    // Poin tidak bertambah
    expect(UserPoint::where('user_id', $user->id)->value('total_points'))->toBe(3);
});

// ── ROLE MIDDLEWARE ───────────────────────────────────────────────────────────

test('admin bisa akses halaman admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('user biasa tidak bisa akses halaman admin', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

// ── AUDIT LOG ─────────────────────────────────────────────────────────────────

test('audit log menyimpan route method user_agent', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->post(route('admin.users.store'), [
            'nik'                   => '1234567890123456',
            'nama_lengkap'          => 'Test User Audit',
            'email'                 => 'testaudit@garda.com',
            'role'                  => 'user',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'tempat_lahir'          => 'Purwokerto',
            'tanggal_lahir'         => '2000-01-01',
            'jenis_kelamin'         => 'L',
            'alamat'                => 'Jl. Test No. 1',
            'berat_badan'           => 65,
        ]);

    $log = \App\Models\AuditLog::where('action', 'CREATE_USER')->first();
    expect($log)->not->toBeNull();
    expect($log->method)->toBe('POST');
    expect($log->route)->not->toBeNull();
});
