<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\User;
use App\Models\UserPoint;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Metrik utama
        $totalWarga = User::where('role', 'user')->count();
        $totalLogs  = HealthLog::count();
        $totalPoin  = UserPoint::sum('total_points');
        $totalDaun  = UserPoint::sum('total_leaves');

        // Tren 7 hari: hitung rata-rata sistolik/diastolik di PHP
        // (aman untuk MySQL & SQLite)
        $logs7Hari = HealthLog::whereNotNull('tekanan_darah')
            ->where('tanggal_input', '>=', now()->subDays(6)->toDateString())
            ->get(['tanggal_input', 'tekanan_darah']);

        // Kelompokkan per tanggal
        $grouped = $logs7Hari->groupBy(fn($l) => $l->tanggal_input->format('Y-m-d'));

        $chartTD = ['labels' => [], 'sistolik' => [], 'diastolik' => []];

        // Pastikan 7 hari terakhir selalu muncul (meskipun tidak ada data)
        for ($i = 6; $i >= 0; $i--) {
            $tgl    = now()->subDays($i)->format('Y-m-d');
            $label  = Carbon::parse($tgl)->format('d M');
            $logsHari = $grouped->get($tgl, collect());

            $sistolikList  = $logsHari->map(fn($l) => (int) explode('/', $l->tekanan_darah)[0])->filter();
            $diastolikList = $logsHari->map(fn($l) => isset(explode('/', $l->tekanan_darah)[1]) ? (int) explode('/', $l->tekanan_darah)[1] : null)->filter();

            $chartTD['labels'][]    = $label;
            $chartTD['sistolik'][]  = $sistolikList->count() ? round($sistolikList->average()) : null;
            $chartTD['diastolik'][] = $diastolikList->count() ? round($diastolikList->average()) : null;
        }

        return view('puskesmas.dashboard', compact(
            'totalWarga', 'totalLogs', 'totalPoin', 'totalDaun', 'chartTD'
        ));
    }
}
