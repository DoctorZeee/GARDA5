<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HealthLog;
use App\Models\UserVideoClaim;
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

        $hasCheckedInToday = $user->point &&
            $user->point->last_checkin_date?->toDateString() === Carbon::today()->toDateString();

        // FIX: Hanya tampilkan video yang belum diklaim user ini
        $claimedVideoIds = UserVideoClaim::where('user_id', $user->id)
            ->pluck('video_id');

        $videos = Video::where('is_active', true)
            ->whereNotIn('id', $claimedVideoIds)
            ->get();

        return view('user.dashboard', compact('user', 'hasLoggedToday', 'hasCheckedInToday', 'videos'));
    }
}
