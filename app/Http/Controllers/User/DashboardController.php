<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\Video;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load(['wilayah', 'point']);
        
        // Cek apakah sudah mengisi log hari ini
        $hasLoggedToday = HealthLog::where('user_id', $user->id)
            ->whereDate('tanggal_input', Carbon::today())
            ->exists();

        // Cek status check-in harian
        $hasCheckedInToday = $user->point && $user->point->last_checkin_date === Carbon::today()->toDateString();

        // Ambil video aktif (Untuk production, kita bisa filter yang belum ditonton, namun untuk MVP kita tampilkan semua)
        $videos = Video::where('is_active', true)->get();

        return view('user.dashboard', compact('user', 'hasLoggedToday', 'hasCheckedInToday', 'videos'));
    }
}