<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreHealthLogRequest;
use App\Models\HealthLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HealthLogController extends Controller
{
    public function store(StoreHealthLogRequest $request)
    {
        $user = auth()->user();

        // Validasi Anti-Spam: 1 Log per hari
        if (HealthLog::where('user_id', $user->id)->whereDate('tanggal_input', Carbon::today())->exists()) {
            return back()->with('error', 'Anda sudah mencatat data kesehatan hari ini.');
        }

        // Kalkulasi Status Hipertensi Otomatis
        $statusHipertensi = 'Normal';
        if ($request->tekanan_darah) {
            [$sistolik, $diastolik] = explode('/', $request->tekanan_darah);
            if ($sistolik >= 160 || $diastolik >= 100) {
                $statusHipertensi = 'Berat';
            } elseif ($sistolik >= 140 || $diastolik >= 90) {
                $statusHipertensi = 'Sedang';
            } elseif ($sistolik >= 130 || $diastolik >= 85) {
                $statusHipertensi = 'Ringan';
            }
        }

        // DB Transaction: Pastikan data log dan penambahan poin tidak terputus di tengah jalan
        DB::transaction(function () use ($request, $user, $statusHipertensi) {
            HealthLog::create([
                'user_id' => $user->id,
                'tekanan_darah' => $request->tekanan_darah,
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'konsumsi_garam' => $request->konsumsi_garam,
                'status_hipertensi' => $statusHipertensi,
                'keluhan' => $request->keluhan,
                'tanggal_input' => Carbon::today(),
            ]);

            // Berikan reward +1 Poin dan +1 Daun
            $user->point()->increment('total_points', 1);
            $user->point()->increment('total_leaves', 1);
        });

        return back()->with('success', 'Data Kesehatan Berhasil Disimpan! +1 Daun tumbuh di pohon Anda.');
    }
}