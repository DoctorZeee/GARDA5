<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kader = auth()->user();

        // SECURITY: Kader HANYA BISA menarik data Log Kesehatan dari User yang wilayah_id-nya sama.
        // PERFORMA: Menggunakan Eager Loading untuk 'user', 'user.wilayah', dan 'user.point' (Cegah N+1)
        $wargaLogs = HealthLog::with(['user.wilayah', 'user.point'])
            ->whereHas('user', function ($query) use ($kader) {
                $query->where('role', 'user')
                      ->where('wilayah_id', $kader->wilayah_id);
            })
            ->orderBy('tanggal_input', 'desc')
            ->get(); // Untuk DataTable Client-side, kita kirimkan seluruh data hasil filter wilayah.

        return view('kader.dashboard', compact('wargaLogs', 'kader'));
    }
}