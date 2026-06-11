<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('is-kader');

        $kader = auth()->user();

        if (! $kader->wilayah_id) {
            return view('kader.dashboard', [
                'wargaLogs' => collect(),
                'kader'     => $kader,
            ])->with('error', 'Akun Anda belum terdaftar di wilayah manapun. Hubungi Admin.');
        }

        // SECURITY: Scoped strictly to kader's wilayah (data isolation)
        // PERF: Eager load to avoid N+1
        $wargaLogs = HealthLog::with(['user:id,nama_lengkap,wilayah_id', 'user.point'])
            ->whereHas('user', fn ($q) =>
                $q->where('role', 'user')
                  ->where('wilayah_id', $kader->wilayah_id)
            )
            ->orderByDesc('tanggal_input')
            ->paginate(25);

        return view('kader.dashboard', compact('wargaLogs', 'kader'));
    }
}
