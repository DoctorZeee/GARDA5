<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function checkin(Request $request)
    {
        $pointRecord = auth()->user()->point;

        if (!$pointRecord) {
            return back()->with('error', 'Data poin tidak ditemukan. Hubungi admin.');
        }

        if ($pointRecord->last_checkin_date === Carbon::today()->toDateString()) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        $pointRecord->update([
            'last_checkin_date' => Carbon::today()->toDateString(),
            'total_points'      => $pointRecord->total_points + 1,
            'total_leaves'      => $pointRecord->total_leaves + 1,
        ]);

        return back()->with('success', 'Check-In Sukses! +1 Poin berhasil diklaim. 🌿');
    }

    public function claimVideo(Request $request, Video $video)
    {
        $pointRecord = auth()->user()->point;

        if (!$pointRecord) {
            return back()->with('error', 'Data poin tidak ditemukan. Hubungi admin.');
        }

        $reward = max(1, (int) $video->points_reward);

        $pointRecord->increment('total_points', $reward);
        $pointRecord->increment('total_leaves', $reward);

        return back()->with('success', "Poin edukasi +{$reward} berhasil diklaim! 🎉");
    }
}
