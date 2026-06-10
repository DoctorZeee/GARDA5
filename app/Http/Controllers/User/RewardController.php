<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserVideoClaim;
use App\Models\Video;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardController extends Controller
{
    /**
     * Daily check-in.
     *
     * Perlindungan duplikat berlapis:
     *  1. Fast-path check sebelum transaksi (hemat lock)
     *  2. lockForUpdate() di dalam transaksi (cegah race condition)
     *  3. applyCheckin() di model yang juga memverifikasi ulang
     */
    public function checkin(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Fast-path: hindari buka transaksi kalau sudah jelas check-in
        if ($user->point?->hasCheckedInToday()) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        try {
            DB::transaction(function () use ($user) {
                $pointRecord = $user->point()
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pointRecord->hasCheckedInToday()) {
                    throw new \RuntimeException('already_checked_in');
                }

                // Logika streak & increment ada di model
                $pointRecord->applyCheckin();
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'already_checked_in') {
                return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
            }
            throw $e;
        }

        AuditLogger::log('CHECKIN', "User {$user->id} check-in harian +1 poin (streak: {$user->fresh()->point->checkin_streak})");

        return back()->with('success', 'Check-In Sukses! +1 Poin berhasil diklaim. 🌿');
    }

    /**
     * Klaim reward setelah menonton video.
     *
     * Perlindungan duplikat:
     *  1. Fast-path EXISTS sebelum transaksi
     *  2. Unique constraint di DB + UniqueConstraintViolationException handler (race condition)
     *  3. Hanya video yang is_active yang bisa diklaim
     */
    public function claimVideo(Request $request, Video $video): RedirectResponse
    {
        if (! $video->is_active) {
            return back()->with('error', 'Video ini tidak tersedia.');
        }

        $user = auth()->user();

        // Fast-path check sebelum masuk transaksi
        if (UserVideoClaim::where('user_id', $user->id)->where('video_id', $video->id)->exists()) {
            return back()->with('error', 'Anda sudah mengklaim video ini sebelumnya.');
        }

        $reward = max(1, (int) $video->points_reward);

        try {
            DB::transaction(function () use ($user, $video, $reward) {
                // INSERT ini akan lempar UniqueConstraintViolationException jika race condition
                UserVideoClaim::create([
                    'user_id'  => $user->id,
                    'video_id' => $video->id,
                ]);

                $pointRecord = $user->point()->lockForUpdate()->first();

                if ($pointRecord) {
                    $pointRecord->increment('total_points', $reward);
                    $pointRecord->increment('total_leaves', $reward);
                } else {
                    $user->point()->create([
                        'total_points' => $reward,
                        'total_leaves' => $reward,
                    ]);
                }
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->with('error', 'Anda sudah mengklaim video ini sebelumnya.');
        }

        AuditLogger::log('CLAIM_VIDEO', "User {$user->id} klaim video {$video->id} \"{$video->title}\" +{$reward} poin");

        return back()->with('success', "Poin edukasi +{$reward} berhasil diklaim! 🎉");
    }
}
