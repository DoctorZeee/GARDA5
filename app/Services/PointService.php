<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use App\Models\UserVideoClaim;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Centralizes all reward/points business logic.
 *
 * All methods that modify points use DB::transaction() + lockForUpdate()
 * to prevent race conditions under concurrent requests.
 */
class PointService
{
    // ─── Check-in ─────────────────────────────────────────────────────────────

    /**
     * Process a daily check-in for the given user.
     *
     * @return array{success: bool, message: string, streak: int}
     */
    public function checkin(User $user): array
    {
        // Fast-path: skip locking if already checked in (reduces DB pressure)
        if ($user->point?->hasCheckedInToday()) {
            return ['success' => false, 'message' => 'Anda sudah melakukan check-in hari ini.', 'streak' => $user->point->checkin_streak];
        }

        try {
            $streak = DB::transaction(function () use ($user): int {
                /** @var UserPoint $record */
                $record = $user->point()->lockForUpdate()->firstOrFail();

                // Re-check inside transaction (race condition guard)
                if ($record->hasCheckedInToday()) {
                    throw new RuntimeException('already_checked_in');
                }

                $record->applyCheckin();

                return $record->fresh()->checkin_streak;
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'already_checked_in') {
                return ['success' => false, 'message' => 'Anda sudah melakukan check-in hari ini.', 'streak' => $user->point->checkin_streak];
            }
            throw $e;
        }

        return [
            'success' => true,
            'message' => "Check-In Sukses! +1 Poin berhasil diklaim. 🌿",
            'streak'  => $streak,
        ];
    }

    // ─── Video Claim ──────────────────────────────────────────────────────────

    /**
     * Process a video reward claim for the given user.
     *
     * @return array{success: bool, message: string}
     */
    public function claimVideo(User $user, Video $video): array
    {
        if (! $video->is_active) {
            return ['success' => false, 'message' => 'Video ini tidak tersedia.'];
        }

        // Fast-path check before entering transaction
        if (UserVideoClaim::where('user_id', $user->id)->where('video_id', $video->id)->exists()) {
            return ['success' => false, 'message' => 'Anda sudah mengklaim video ini sebelumnya.'];
        }

        $reward = max(1, (int) $video->points_reward);

        try {
            DB::transaction(function () use ($user, $video, $reward): void {
                // Will throw UniqueConstraintViolationException on race condition
                UserVideoClaim::create([
                    'user_id'  => $user->id,
                    'video_id' => $video->id,
                ]);

                $record = $user->point()->lockForUpdate()->first();

                if ($record) {
                    $record->increment('total_points', $reward);
                    $record->increment('total_leaves',  $reward);
                } else {
                    $user->point()->create([
                        'total_points' => $reward,
                        'total_leaves' => $reward,
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException) {
            return ['success' => false, 'message' => 'Anda sudah mengklaim video ini sebelumnya.'];
        }

        return [
            'success' => true,
            'message' => "Poin edukasi +{$reward} berhasil diklaim! 🎉",
        ];
    }

    // ─── Health Log Reward ────────────────────────────────────────────────────

    /**
     * Award a point for submitting a health log.
     * Called inside an existing transaction — does NOT open a new one.
     */
    public function awardHealthLogPoint(User $user): void
    {
        if ($user->point) {
            $user->point()->update([
                'total_points' => DB::raw('total_points + 1'),
                'total_leaves' => DB::raw('total_leaves + 1'),
            ]);
        } else {
            $user->point()->create([
                'total_points' => 1,
                'total_leaves' => 1,
            ]);
        }
    }

    // ─── Query Helpers ────────────────────────────────────────────────────────

    public function hasClaimedVideo(User $user, Video $video): bool
    {
        return UserVideoClaim::where('user_id', $user->id)
            ->where('video_id', $video->id)
            ->exists();
    }

    public function getUserPoints(User $user): UserPoint
    {
        return $user->point ?? $user->point()->create([
            'total_points'   => 0,
            'total_leaves'   => 0,
            'checkin_streak' => 0,
            'checkin_count'  => 0,
        ]);
    }
}
