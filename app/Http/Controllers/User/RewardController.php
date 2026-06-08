<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function checkin(Request $request)
    {
        $pointRecord = auth()->user()->point;

        if ($pointRecord->last_checkin_date === Carbon::today()->toDateString()) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        $pointRecord->update([
            'last_checkin_date' => Carbon::today()->toDateString(),
            'total_points' => $pointRecord->total_points + 1,
            'total_leaves' => $pointRecord->total_leaves + 1,
        ]);

        return back()->with('success', 'Check-In Sukses! +1 Poin berhasil diklaim.');
    }

    public function claimVideo(Request $request, $videoId)
    {
        // Dalam implementasi nyata, kita catat di tabel user_video_logs
        // Untuk MVP ini, kita berikan poin langsung. Validasi disederhanakan.
        $pointRecord = auth()->user()->point;
        
        $pointRecord->increment('total_points', 1);
        $pointRecord->increment('total_leaves', 1);

        return response()->json([
            'status' => 'success',
            'message' => 'Poin edukasi berhasil diklaim!',
            'new_points' => $pointRecord->total_points,
            'new_leaves' => $pointRecord->total_leaves
        ]);
    }
}