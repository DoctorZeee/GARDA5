<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\Video;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load(['wilayah', 'point']);

        $hasLoggedToday = HealthLog::where('user_id', $user->id)
            ->whereDate('tanggal_input', Carbon::today())
            ->exists();

        $hasCheckedInToday = $user->point?->hasCheckedInToday() ?? false;

        // FIX: Use whereDoesntHave() to eliminate the extra pluck() query (N+1 optimisation)
        // This produces a single SQL with a NOT EXISTS subquery instead of two round trips.
        $videos = Video::active()
            ->whereDoesntHave('claims', fn ($q) => $q->where('user_id', $user->id))
            ->get();

        return view('user.dashboard', compact('user', 'hasLoggedToday', 'hasCheckedInToday', 'videos'));
    }
}
