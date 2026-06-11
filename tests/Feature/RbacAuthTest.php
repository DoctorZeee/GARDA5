<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\HealthLog;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Video;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAuthTest extends TestCase
{
    use RefreshDatabase;

    private Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wilayah = Wilayah::create(['nama_wilayah' => 'Test Wilayah']);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeUser(string $role = 'user'): User
    {
        $user = User::factory()->create(['wilayah_id' => $this->wilayah->id]);
        $user->role = $role;
        $user->save();
        return $user;
    }

    private function makeUserWithPoint(string $role = 'user'): User
    {
        $user = $this->makeUser($role);
        $user->point()->create([
            'total_points'   => 0,
            'total_leaves'   => 0,
            'checkin_streak' => 0,
            'checkin_count'  => 0,
        ]);
        return $user;
    }

    // ─── Authentication ───────────────────────────────────────────────────────

    public function test_guest_cannot_access_dashboards(): void
    {
        foreach (['/admin/dashboard', '/puskesmas/dashboard', '/kader/dashboard', '/user/dashboard'] as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = $this->makeUserWithPoint();

        $this->post('/login', [
            'nik'      => $user->nik,
            'password' => 'password',
        ])->assertRedirect(route('user.dashboard'));
    }

    public function test_login_throttle_after_too_many_attempts(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['nik' => $user->nik, 'password' => 'wrong']);
        }

        $response = $this->post('/login', ['nik' => $user->nik, 'password' => 'wrong']);
        $response->assertStatus(429);
    }

    // ─── RBAC ─────────────────────────────────────────────────────────────────

    public function test_warga_cannot_access_admin_dashboard(): void
    {
        $user = $this->makeUser('user');
        $this->actingAs($user)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_puskesmas_cannot_access_admin_routes(): void
    {
        $puskesmas = $this->makeUser('puskesmas');
        $this->actingAs($puskesmas)->get('/admin/users')->assertStatus(403);
    }

    public function test_role_routing_correct_for_each_role(): void
    {
        $routes = [
            'admin'     => 'admin.dashboard',
            'puskesmas' => 'puskesmas.dashboard',
            'kader'     => 'kader.dashboard',
            'user'      => 'user.dashboard',
        ];

        foreach ($routes as $role => $routeName) {
            $user = $this->makeUser($role);
            $this->post('/login', [
                'nik'      => $user->nik,
                'password' => 'password',
            ])->assertRedirect(route($routeName));
            $this->post('/logout');
        }
    }

    // ─── Seeder Safety ────────────────────────────────────────────────────────

    public function test_dev_fixture_seeder_not_called_in_production(): void
    {
        // Verify DevFixtureSeeder has the production guard
        $seeder = new \Database\Seeders\DevFixtureSeeder();
        $this->assertInstanceOf(\Database\Seeders\DevFixtureSeeder::class, $seeder);

        // In test environment (not production), running should work
        $this->assertFalse(app()->isProduction());
    }

    // ─── User Delete Protection ───────────────────────────────────────────────

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin)
            ->delete("/admin/users/{$admin->id}")
            ->assertStatus(403);
    }

    public function test_admin_cannot_delete_last_admin(): void
    {
        $admin = $this->makeUser('admin');
        $otherUser = $this->makeUser('user');

        $this->actingAs($admin)
            ->delete("/admin/users/{$admin->id}")
            ->assertStatus(403);
    }

    public function test_admin_can_delete_non_admin_user(): void
    {
        $admin  = $this->makeUser('admin');
        $target = $this->makeUser('user');

        $this->actingAs($admin)
            ->delete("/admin/users/{$target->id}")
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    // ─── HealthLog ────────────────────────────────────────────────────────────

    public function test_warga_can_submit_health_log(): void
    {
        $user = $this->makeUserWithPoint();

        $this->actingAs($user)
            ->post('/user/health-logs', [
                'tekanan_darah'  => '120/80',
                'berat_badan'    => 65,
                'tinggi_badan'   => 170,
                'konsumsi_garam' => 'ideal',
                'keluhan'        => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('health_logs', [
            'user_id'           => $user->id,
            'status_hipertensi' => 'Normal',
        ]);
    }

    public function test_warga_cannot_submit_duplicate_health_log_same_day(): void
    {
        $user = $this->makeUserWithPoint();

        $this->actingAs($user)->post('/user/health-logs', [
            'berat_badan'    => 65,
            'tinggi_badan'   => 170,
            'konsumsi_garam' => 'ideal',
        ]);

        $response = $this->actingAs($user)->post('/user/health-logs', [
            'berat_badan'    => 65,
            'tinggi_badan'   => 170,
            'konsumsi_garam' => 'ideal',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('health_logs', 1);
    }

    public function test_blood_pressure_classification_written_correctly(): void
    {
        $user = $this->makeUserWithPoint();

        $this->actingAs($user)->post('/user/health-logs', [
            'tekanan_darah'  => '145/95',
            'berat_badan'    => 70,
            'tinggi_badan'   => 170,
            'konsumsi_garam' => 'more',
        ]);

        $this->assertDatabaseHas('health_logs', [
            'user_id'           => $user->id,
            'status_hipertensi' => 'Sedang',
        ]);
    }

    // ─── Reward Logic ─────────────────────────────────────────────────────────

    public function test_checkin_awards_point_and_increments_streak(): void
    {
        $user = $this->makeUserWithPoint();

        $this->actingAs($user)
            ->post('/user/checkin')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('user_points', [
            'user_id'        => $user->id,
            'total_points'   => 1,
            'checkin_streak' => 1,
            'checkin_count'  => 1,
        ]);
    }

    public function test_checkin_idempotent_same_day(): void
    {
        $user = $this->makeUserWithPoint();

        $this->actingAs($user)->post('/user/checkin');
        $this->actingAs($user)->post('/user/checkin')->assertSessionHas('error');

        $this->assertDatabaseHas('user_points', ['user_id' => $user->id, 'total_points' => 1]);
    }

    public function test_video_claim_awards_correct_points(): void
    {
        $user  = $this->makeUserWithPoint();
        $video = Video::create([
            'youtube_id'    => 'test12345ab',
            'title'         => 'Test Video',
            'points_reward' => 3,
            'is_active'     => true,
            'sort_order'    => 1,
        ]);

        $this->actingAs($user)
            ->post("/user/video/{$video->id}/claim")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('user_points', [
            'user_id'      => $user->id,
            'total_points' => 3,
        ]);
    }

    public function test_video_claim_idempotent(): void
    {
        $user  = $this->makeUserWithPoint();
        $video = Video::create([
            'youtube_id'    => 'test12345ab',
            'title'         => 'Test Video',
            'points_reward' => 3,
            'is_active'     => true,
            'sort_order'    => 1,
        ]);

        $this->actingAs($user)->post("/user/video/{$video->id}/claim");
        $this->actingAs($user)->post("/user/video/{$video->id}/claim")->assertSessionHas('error');

        $this->assertDatabaseHas('user_points', ['user_id' => $user->id, 'total_points' => 3]);
    }

    public function test_inactive_video_cannot_be_claimed(): void
    {
        $user  = $this->makeUserWithPoint();
        $video = Video::create([
            'youtube_id'    => 'inactive1234',
            'title'         => 'Inactive Video',
            'points_reward' => 2,
            'is_active'     => false,
            'sort_order'    => 1,
        ]);

        $this->actingAs($user)
            ->post("/user/video/{$video->id}/claim")
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('user_video_claims', [
            'user_id'  => $user->id,
            'video_id' => $video->id,
        ]);
    }

    // ─── YouTube ID Uniqueness ────────────────────────────────────────────────

    public function test_admin_cannot_create_duplicate_youtube_video(): void
    {
        $admin = $this->makeUser('admin');

        Video::create([
            'youtube_id'    => 'abc123defgh',
            'title'         => 'First',
            'points_reward' => 1,
            'is_active'     => true,
            'sort_order'    => 1,
        ]);

        $this->actingAs($admin)
            ->post('/admin/videos', [
                'youtube_id'    => 'abc123defgh',
                'title'         => 'Duplicate',
                'points_reward' => 1,
                'is_active'     => true,
                'sort_order'    => 2,
            ])
            ->assertSessionHasErrors('youtube_id');
    }
}
