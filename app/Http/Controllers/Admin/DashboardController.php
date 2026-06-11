<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\User;
use App\Models\Wilayah;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private const TTL = 60 * 15; // 15 minutes

    public function index()
    {
        Gate::authorize('is-admin');

        // ─── KPI ──────────────────────────────────────────────────────────────
        $kpi = Cache::remember('admin_kpi_v5', self::TTL, function () {
            $tekananDarah = HealthLog::whereNotNull('tekanan_darah')->pluck('tekanan_darah');

            $sistolikList = $tekananDarah->map(function (string $td) {
                $parts = explode('/', $td, 2);
                return (isset($parts[0]) && is_numeric($parts[0])) ? (int) $parts[0] : null;
            })->filter();

            return [
                'total_warga'  => User::warga()->count(),
                'total_log'    => HealthLog::count(),
                'high_sodium'  => HealthLog::where('konsumsi_garam', 'more')->count(),
                'avg_sistolik' => $sistolikList->count() > 0 ? round($sistolikList->average(), 1) : 0,
            ];
        });

        // ─── Gender Stats ─────────────────────────────────────────────────────
        $genderStats = Cache::remember('admin_gender_stats_v5', self::TTL, function () {
            if (! Schema::hasColumn('users', 'jenis_kelamin')) {
                return ['L' => 0, 'P' => 0];
            }

            return User::warga()
                ->select('jenis_kelamin', DB::raw('count(*) as total'))
                ->groupBy('jenis_kelamin')
                ->pluck('total', 'jenis_kelamin')
                ->toArray();
        });

        // ─── Hipertensi Stats ─────────────────────────────────────────────────
        $hipertensiStats = Cache::remember('admin_hipertensi_v5', self::TTL, function () {
            return HealthLog::select('status_hipertensi', DB::raw('count(*) as total'))
                ->groupBy('status_hipertensi')
                ->pluck('total', 'status_hipertensi')
                ->toArray();
        });

        // ─── Wilayah Stats ────────────────────────────────────────────────────
        $wilayahStats = Cache::remember('admin_wilayah_v5', self::TTL, function () {
            return Wilayah::withCount([
                'users' => fn ($q) => $q->where('role', 'user'),
            ])->get()->toArray();
        });

        // ─── 7-Day Trend (no cache — real-time is important here) ─────────────
        $trenKesehatan = HealthLog::select(
                DB::raw('DATE(tanggal_input) as date'),
                DB::raw('count(*) as count')
            )
            ->where('tanggal_input', '>=', now()->subDays(6)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // ─── Audit Logs (safe columns only) ──────────────────────────────────
        $auditLogs = AuditLogger::recent(limit: 8);

        return view('admin.dashboard', compact(
            'kpi',
            'genderStats',
            'hipertensiStats',
            'wilayahStats',
            'trenKesehatan',
            'auditLogs',
        ));
    }
}
