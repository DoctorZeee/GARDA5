<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HealthLog;
use App\Models\AuditLog; // Pastikan model ini di-import
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Utama
        $totalWarga = User::where('role', 'user')->count();
        $totalLog = HealthLog::count();
        $avgGaram = HealthLog::where('konsumsi_garam', 'more')->count();

        // 2. Data Grafik Batang (Status Hipertensi)
        $hipertensiStats = HealthLog::select('status_hipertensi', DB::raw('count(*) as total'))
            ->groupBy('status_hipertensi')->pluck('total', 'status_hipertensi');

        // 3. Data Grafik Garis (Tren Kesehatan 7 Hari Terakhir)
        $trenKesehatan = HealthLog::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')->orderBy('date', 'desc')->take(7)->get();

        // 4. Data per Wilayah
        $wilayahStats = Wilayah::withCount('users')->get();

        $auditLogs = AuditLog::latest()->take(10)->get();

        return view('admin.dashboard', compact('totalWarga', 'totalLog', 'avgGaram', 'hipertensiStats', 'trenKesehatan', 'wilayahStats', 'auditLogs'));
    }
}
