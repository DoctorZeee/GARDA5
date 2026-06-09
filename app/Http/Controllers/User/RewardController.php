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
     * FIX: Bungkus dalam DB::transaction() + lockForUpdate()
     * Mencegah race condition dua request bersamaan lolos cek last_checkin_date.
     */
    public function checkin(Request $request): RedirectResponse
    {
        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        try {
            DB::transaction(function () use ($user, $today) {
                $pointRecord = $user->point()
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pointRecord->last_checkin_date?->toDateString() === $today) {
                    throw new \RuntimeException('already_checked_in');
                }

                $pointRecord->update([
                    'last_checkin_date' => $today,
                    'total_points'      => $pointRecord->total_points + 1,
                    'total_leaves'      => $pointRecord->total_leaves + 1,
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'already_checked_in') {
                return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
            }
            throw $e;
        }

        AuditLogger::log('CHECKIN', "User {$user->id} checkin harian +1 poin");

        return back()->with('success', 'Check-In Sukses! +1 Poin berhasil diklaim. 🌿');
    }

    /**
     * FIX 1: Cek duplikat via tabel user_video_claims.
     * FIX 2: Bungkus dalam DB::transaction() untuk atomicity.
     * Sebelumnya user bisa klaim video sama berkali-kali → poin tidak terbatas.
     */
    public function claimVideo(Request $request, Video $video): RedirectResponse
    {
        $user = auth()->user();

        // Fast-path check sebelum masuk transaksi
        $alreadyClaimed = UserVideoClaim::where('user_id', $user->id)
            ->where('video_id', $video->id)
            ->exists();

        if ($alreadyClaimed) {
            return back()->with('error', 'Anda sudah mengklaim video ini sebelumnya.');
        }

        $reward = max(1, (int) $video->points_reward);

        try {
            DB::transaction(function () use ($user, $video, $reward) {
                // Unique constraint di DB mencegah race condition dua request bersamaan
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
            // Race condition: request lain sudah insert duluan
            return back()->with('error', 'Anda sudah mengklaim video ini sebelumnya.');
        }

        AuditLogger::log('CLAIM_VIDEO', "User {$user->id} klaim video {$video->id} +{$reward} poin");

        return back()->with('success', "Poin edukasi +{$reward} berhasil diklaim! 🎉");
    }
}
