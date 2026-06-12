<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Video;
use App\Models\Wilayah;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private const TTL       = 60 * 15;  // 15-min cache for heavy aggregates
    private const TTL_SHORT = 60 * 2;   // 2-min cache for semi-live panels

    /**
     * Single cache-bust prefix. Increment CACHE_VERSION in .env (or here)
     * to invalidate ALL dashboard cache keys at once — no more per-key versioning.
     *
     * Default: "d1". Change to "d2", "d3" etc. whenever a breaking deploy lands.
     */
    private static function cv(): string
    {
        return 'dash_' . config('app.cache_version', 'd1') . '_';
    }

    public function index(Request $request)
    {
        Gate::authorize('is-admin');

        $cv = self::cv();

        // ── Global KPI ────────────────────────────────────────────────────────
        $kpi = Cache::remember("{$cv}kpi", self::TTL, function () {
            $sistolikList = HealthLog::whereNotNull('tekanan_darah')
                ->whereNull('deleted_at')
                ->pluck('tekanan_darah')
                ->map(function (string $td) {
                    $parts = explode('/', $td, 2);
                    return (isset($parts[0]) && is_numeric($parts[0])) ? (int) $parts[0] : null;
                })
                ->filter();

            $now = now();

            return [
                'total_warga'          => User::warga()->count(),
                'active_warga'         => User::warga()
                    ->whereHas('healthLogs', fn ($q) =>
                        $q->where('tanggal_input', '>=', $now->copy()->subDays(30)->toDateString())
                    )->count(),
                'new_warga_this_month' => User::warga()
                    ->whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->count(),
                'total_non_warga'      => User::where('role', '!=', UserRole::User->value)->count(),
                'total_log'            => HealthLog::count(),
                'logs_this_month'      => HealthLog::whereYear('tanggal_input', $now->year)
                    ->whereMonth('tanggal_input', $now->month)
                    ->count(),
                'high_sodium'          => HealthLog::where('konsumsi_garam', 'more')->count(),
                'avg_sistolik'         => $sistolikList->count() > 0
                    ? round($sistolikList->average(), 1)
                    : 0,
                'total_leaves_sum'     => UserPoint::sum('total_leaves'),
                'total_points_sum'     => UserPoint::sum('total_points'),
                'total_videos'         => Video::where('is_active', true)->count(),
            ];
        });

        // ── Gender Stats ──────────────────────────────────────────────────────
        $genderStats = Cache::remember("{$cv}gender", self::TTL, function () {
            if (! Schema::hasColumn('users', 'jenis_kelamin')) {
                return ['L' => 0, 'P' => 0];
            }
            return User::warga()
                ->select('jenis_kelamin', DB::raw('count(*) as total'))
                ->groupBy('jenis_kelamin')
                ->pluck('total', 'jenis_kelamin')
                ->toArray();
        });

        // ── Hypertension Stats ────────────────────────────────────────────────
        $hipertensiStats = Cache::remember("{$cv}hipertensi", self::TTL, function () {
            return HealthLog::select('status_hipertensi', DB::raw('count(*) as total'))
                ->groupBy('status_hipertensi')
                ->pluck('total', 'status_hipertensi')
                ->toArray();
        });

        // ── Wilayah Stats ─────────────────────────────────────────────────────
        $wilayahStats = Cache::remember("{$cv}wilayah", self::TTL, function () {
            return Wilayah::withCount([
                'users' => fn ($q) => $q->where('role', UserRole::User->value),
            ])->get()->toArray();
        });

        // ── 7-Day Health Log Trend (intentionally NOT cached — real-time) ─────
        $trenKesehatan = HealthLog::select(
                DB::raw('DATE(tanggal_input) as date'),
                DB::raw('count(*) as count')
            )
            ->where('tanggal_input', '>=', now()->subDays(6)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // ── New users trend (last 6 months) ──────────────────────────────────
        $newUsersTrend = Cache::remember("{$cv}new_users_trend", self::TTL, function () {
            $monthExpr = self::monthExpression('created_at');

            return User::warga()
                ->select(
                    DB::raw("{$monthExpr} as month"),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->toArray();
        });

        // ── Top active users (most logs this month) ───────────────────────────
        // Uses DB::table() + selectRaw with a correlated subcount to avoid the
        // leftJoin + ->with() column-collision bug on MySQL. Returns stdClass rows.
        // top_users: stored as plain array-of-arrays in cache (never objects).
        // Objects serialized in Laravel's file/database cache can deserialize
        // as strings when the class is not found, causing "property on string".
        // Plain arrays are always safe across cache drivers.
        $topActiveUsers = Cache::remember("{$cv}top_users", self::TTL_SHORT, function () {
            $startOfMonth = now()->startOfMonth()->toDateString();

            $rows = DB::table('users')
                ->selectRaw(
                    'users.id, users.nama_lengkap, users.nik, users.wilayah_id,
                     (SELECT count(*) FROM health_logs
                      WHERE health_logs.user_id = users.id
                        AND health_logs.tanggal_input >= ?
                        AND health_logs.deleted_at IS NULL) as log_count',
                    [$startOfMonth]
                )
                ->where('users.role', 'user')
                ->whereNull('users.deleted_at')
                ->orderByDesc('log_count')
                ->limit(5)
                ->get();

            $wilayahMap = DB::table('wilayahs')
                ->whereIn('id', $rows->pluck('wilayah_id')->filter()->unique()->values())
                ->pluck('nama_wilayah', 'id')
                ->toArray();

            // Cast to plain associative arrays — safe to serialize in any cache driver
            return $rows->map(fn ($row) => [
                'id'           => $row->id,
                'nama_lengkap' => $row->nama_lengkap,
                'nik'          => $row->nik,
                'wilayah_id'   => $row->wilayah_id,
                'wilayah_name' => $wilayahMap[$row->wilayah_id] ?? null,
                'log_count'    => (int) $row->log_count,
            ])->toArray();
        });

        // ── Inactive users (no log in 30 days) ────────────────────────────────
        // inactive: also stored as plain array-of-arrays for the same reason.
        $inactiveUsers = Cache::remember("{$cv}inactive", self::TTL_SHORT, function () {
            $users = User::warga()
                ->whereDoesntHave('healthLogs', fn ($q) =>
                    $q->where('tanggal_input', '>=', now()->subDays(30)->toDateString())
                )
                ->select('id', 'nama_lengkap', 'nik', 'created_at', 'wilayah_id')
                ->with('wilayah:id,nama_wilayah')
                ->orderByDesc('created_at')
                ->take(10)
                ->get();

            return $users->map(fn ($u) => [
                'id'           => $u->id,
                'nama_lengkap' => $u->nama_lengkap,
                'nik'          => $u->nik,
                'created_at'   => $u->created_at?->toDateString(),
                'wilayah_name' => $u->wilayah?->nama_wilayah,
            ])->toArray();
        });

        // ── Audit Logs (not cached — must always be real-time) ────────────────
        $auditLogs = AuditLogger::recent(limit: 10);

        // ── Recent health log entries (not cached — real-time feed) ───────────
        $recentHealthLogs = HealthLog::with(['user:id,nama_lengkap,nik'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'kpi',
            'genderStats',
            'hipertensiStats',
            'wilayahStats',
            'trenKesehatan',
            'newUsersTrend',
            'topActiveUsers',
            'inactiveUsers',
            'auditLogs',
            'recentHealthLogs',
        ));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Returns a SQL expression to format a datetime column as 'YYYY-MM'.
     * MySQL/MariaDB → DATE_FORMAT  |  SQLite → strftime
     */
    private static function monthExpression(string $column): string
    {
        return match (DB::getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            default  => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}