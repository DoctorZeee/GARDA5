<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HealthLog;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Agregasi Metrik Utama (KISS: Hitung langsung dari DB)
        $totalWarga = User::where('role', 'user')->count();
        $totalLogs = HealthLog::count();
        $totalPoin = UserPoint::sum('total_points');
        $totalDaun = UserPoint::sum('total_leaves');

        // 2. Data Chart: Tren Tekanan Darah Rata-Rata 7 Hari Terakhir
        $trendTD = HealthLog::select(
                DB::raw('DATE(tanggal_input) as date'),
                // Mengambil nilai Sistolik (sebelum '/') dan Diastolik (sesudah '/')
                // Catatan: Pada DB modern (MariaDB/MySQL) kita bisa menggunakan SUBSTRING_INDEX
                DB::raw('AVG(CAST(SUBSTRING_INDEX(tekanan_darah, "/", 1) AS UNSIGNED)) as avg_sistolik'),
                DB::raw('AVG(CAST(SUBSTRING_INDEX(tekanan_darah, "/", -1) AS UNSIGNED)) as avg_diastolik')
            )
            ->whereNotNull('tekanan_darah')
            ->where('tanggal_input', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Siapkan Array untuk Chart.js di Blade
        $chartTD = [
            'labels' => $trendTD->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toArray(),
            'sistolik' => $trendTD->pluck('avg_sistolik')->map(fn($v) => round($v))->toArray(),
            'diastolik' => $trendTD->pluck('avg_diastolik')->map(fn($v) => round($v))->toArray(),
        ];

        return view('puskesmas.dashboard', compact('totalWarga', 'totalLogs', 'totalPoin', 'totalDaun', 'chartTD'));
    }
}