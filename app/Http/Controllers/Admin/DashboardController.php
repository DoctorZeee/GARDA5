<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HealthLog;
use App\Models\AuditLog;
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $ttl = 60 * 15; // 15 Menit

        // --- [ SECTION 1: KPI UTAMA ] ---
        $kpi = Cache::remember('admin_kpi_v4', $ttl, function () {
            $tekananDarah = HealthLog::whereNotNull('tekanan_darah')->pluck('tekanan_darah');
            $sistolikList = $tekananDarah->map(function ($td) {
                $parts = explode('/', $td);
                return (isset($parts[0]) && is_numeric($parts[0])) ? (int) $parts[0] : null;
            })->filter();

            $avgSistolik = $sistolikList->count() > 0 ? round($sistolikList->average(), 1) : 0;

            return [
                'total_warga' => User::where('role', 'user')->count(),
                'total_log' => HealthLog::count(),
                'high_sodium' => HealthLog::where('konsumsi_garam', 'more')->count(),
                'avg_sistolik' => $avgSistolik,
            ];
        });

        // --- [ SECTION 2: DEMOGRAFI & FISIOLOGIS ] ---
        $genderStats = Cache::remember('admin_gender_stats_v4', $ttl, function () {
            if (Schema::hasColumn('users', 'jenis_kelamin')) {
                return User::where('role', 'user')
                    ->select('jenis_kelamin', DB::raw('count(*) as total'))
                    ->groupBy('jenis_kelamin')
                    ->pluck('total', 'jenis_kelamin')
                    ->toArray();
            }
            return ['Laki-laki' => 0, 'Perempuan' => 0];
        });

        // --- [ SECTION 3: KLINIS & STATUS HIPERTENSI ] ---
        $hipertensiStats = Cache::remember('admin_hipertensi_v4', $ttl, function () {
            return HealthLog::select('status_hipertensi', DB::raw('count(*) as total'))
                ->groupBy('status_hipertensi')
                ->pluck('total', 'status_hipertensi')
                ->toArray();
        });

        // --- [ SECTION 4: SPASIAL (WILAYAH BINAAN) ] ---
        $wilayahStats = Cache::remember('admin_wilayah_v4', $ttl, function () {
            return Wilayah::withCount(['users' => function ($query) {
                $query->where('role', 'user');
            }])->get()->toArray();
        });

        // --- [ SECTION 5: TREN WAKTU (7 HARI TERAKHIR) ] ---
        $trenKesehatan = Cache::remember('admin_tren_v4', $ttl, function () {
            return HealthLog::select(DB::raw('DATE(tanggal_input) as date'), DB::raw('count(*) as count'))
                ->where('tanggal_input', '>=', now()->subDays(6)->toDateString())
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->toArray();
        });

        // --- [ SECTION 6: REAL-TIME STREAM (Tanpa Cache) ] ---
        $currentRole = Auth::user()->role;
        $auditLogs = AuditLog::with('user')  // Tidak membatasi kolom, ambil semua
            ->when($currentRole !== 'admin', function ($query) {
                return $query->whereHas('user', function($q) {
                    $q->where('role', '!=', 'admin');
                });
            })
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'kpi',
            'genderStats',
            'hipertensiStats',
            'wilayahStats',
            'trenKesehatan',
            'auditLogs',
            'currentRole'
        ));
    }
}