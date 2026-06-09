<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;

class DashboardController extends Controller
{
    public function index()
    {
        $kader = auth()->user();

        // Pastikan kader punya wilayah_id sebelum query
        if (!$kader->wilayah_id) {
            return view('kader.dashboard', [
                'wargaLogs' => collect(),
                'kader'     => $kader,
            ])->with('error', 'Akun Anda belum terdaftar di wilayah manapun. Hubungi Admin.');
        }

        // SECURITY: Filter hanya warga di wilayah kader
        // PERFORMA: eager load user.wilayah & user.point untuk hindari N+1
        // Pagination server-side agar tidak kirim ribuan baris ke browser
        $wargaLogs = HealthLog::with(['user:id,nama_lengkap,wilayah_id', 'user.point'])
            ->whereHas('user', function ($q) use ($kader) {
                $q->where('role', 'user')
                  ->where('wilayah_id', $kader->wilayah_id);
            })
            ->orderBy('tanggal_input', 'desc')
            ->paginate(25);

        return view('kader.dashboard', compact('wargaLogs', 'kader'));
    }
}
