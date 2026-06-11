<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\User;
use App\Models\UserPoint;
use App\ValueObjects\BloodPressure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('is-puskesmas');

        // Core KPIs
        $totalWarga = User::warga()->count();
        $totalLogs  = HealthLog::count();
        $totalPoin  = UserPoint::sum('total_points');
        $totalDaun  = UserPoint::sum('total_leaves');

        // 7-day blood pressure trend (parsed in PHP for DB portability)
        $logs7Hari = HealthLog::whereNotNull('tekanan_darah')
            ->where('tanggal_input', '>=', now()->subDays(6)->toDateString())
            ->get(['tanggal_input', 'tekanan_darah']);

        $grouped = $logs7Hari->groupBy(fn ($l) => $l->tanggal_input->format('Y-m-d'));

        $chartTD = ['labels' => [], 'sistolik' => [], 'diastolik' => []];

        for ($i = 6; $i >= 0; $i--) {
            $tgl      = now()->subDays($i)->format('Y-m-d');
            $logsHari = $grouped->get($tgl, collect());

            $bpList = $logsHari->map(fn ($l) => BloodPressure::tryFromString($l->tekanan_darah))->filter();

            $chartTD['labels'][]    = Carbon::parse($tgl)->format('d M');
            $chartTD['sistolik'][]  = $bpList->count() ? round($bpList->avg(fn ($bp) => $bp->systolic())) : null;
            $chartTD['diastolik'][] = $bpList->count() ? round($bpList->avg(fn ($bp) => $bp->diastolic())) : null;
        }

        return view('puskesmas.dashboard', compact(
            'totalWarga', 'totalLogs', 'totalPoin', 'totalDaun', 'chartTD'
        ));
    }
}
