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

        // Cegah duplikat log dalam satu hari
        if (HealthLog::where('user_id', $user->id)
                     ->whereDate('tanggal_input', Carbon::today())
                     ->exists()) {
            return back()->with('error', 'Anda sudah mencatat data kesehatan hari ini.');
        }

        // Kalkulasi Status Hipertensi Otomatis (dengan validasi format aman)
        $statusHipertensi = 'Normal';
        $tekananDarah = $request->tekanan_darah;

        if ($tekananDarah && str_contains($tekananDarah, '/')) {
            [$sistolik, $diastolik] = array_map('trim', explode('/', $tekananDarah, 2));

            if (is_numeric($sistolik) && is_numeric($diastolik)) {
                $sistolik  = (int) $sistolik;
                $diastolik = (int) $diastolik;

                if ($sistolik >= 160 || $diastolik >= 100) {
                    $statusHipertensi = 'Berat';
                } elseif ($sistolik >= 140 || $diastolik >= 90) {
                    $statusHipertensi = 'Sedang';
                } elseif ($sistolik >= 130 || $diastolik >= 85) {
                    $statusHipertensi = 'Ringan';
                }
            }
        }

        DB::transaction(function () use ($request, $user, $statusHipertensi, $tekananDarah) {
            HealthLog::create([
                'user_id'          => $user->id,
                'tekanan_darah'    => $tekananDarah ?: null,
                'berat_badan'      => $request->berat_badan,
                'tinggi_badan'     => $request->tinggi_badan,
                'konsumsi_garam'   => $request->konsumsi_garam,
                'status_hipertensi'=> $statusHipertensi,
                'keluhan'          => $request->keluhan,
                'tanggal_input'    => Carbon::today(),
            ]);

            // Berikan reward +1 Poin dan +1 Daun (pastikan relasi point ada)
            if ($user->point) {
                $user->point()->increment('total_points', 1);
                $user->point()->increment('total_leaves', 1);
            } else {
                $user->point()->create([
                    'total_points' => 1,
                    'total_leaves' => 1,
                ]);
            }
        });

        return back()->with('success', 'Data Kesehatan Berhasil Disimpan! +1 Daun tumbuh di pohon Anda. 🌿');
    }
}
