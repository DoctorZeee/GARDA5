<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles admin-triggered hard resets of a user's plant/point data.
 *
 * Hard reset: zeros out ALL point columns (total_points, total_leaves,
 * checkin_streak, checkin_count, last_checkin_date).
 *
 * A snapshot is persisted in plant_resets before the reset so the action
 * is fully reversible for audit purposes.
 */
class PlantResetService
{
    /**
     * Hard-reset a user's plant back to its initial state.
     *
     * @param User $targetUser The user whose plant is being reset.
     * @param User $adminUser  The admin performing the reset.
     * @param string|null $reason Optional reason for the reset.
     *
     * @return array{success: bool, message: string}
     */
    public function hardReset(User $targetUser, User $adminUser, ?string $reason = null): array
    {
        if (! $targetUser->isUser()) {
            return [
                'success' => false,
                'message' => 'Hard reset hanya berlaku untuk akun warga (role: user).',
            ];
        }

        try {
            DB::transaction(function () use ($targetUser, $adminUser, $reason) {
                // Lock the record to prevent concurrent resets
                $point = $targetUser->point()->lockForUpdate()->first();

                if (! $point) {
                    // Create a zeroed record if somehow missing
                    $targetUser->point()->create([
                        'total_points'    => 0,
                        'total_leaves'    => 0,
                        'checkin_streak'  => 0,
                        'checkin_count'   => 0,
                        'last_checkin_date' => null,
                    ]);
                    return;
                }

                // Snapshot BEFORE reset
                DB::table('plant_resets')->insert([
                    'target_user_id'       => $targetUser->id,
                    'admin_user_id'        => $adminUser->id,
                    'reason'               => $reason,
                    'before_total_points'  => $point->total_points,
                    'before_total_leaves'  => $point->total_leaves,
                    'before_checkin_streak'=> $point->checkin_streak,
                    'before_checkin_count' => $point->checkin_count,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                // Execute reset
                $point->update([
                    'total_points'      => 0,
                    'total_leaves'      => 0,
                    'checkin_streak'    => 0,
                    'checkin_count'     => 0,
                    'last_checkin_date' => null,
                ]);
            });

            AuditLogger::log(
                'HARD_RESET_PLANT',
                "Admin {$adminUser->nik} reset data pohon warga {$targetUser->nik} ({$targetUser->nama_lengkap}). Alasan: " . ($reason ?? 'tidak dicatat'),
                userId: $adminUser->id
            );

            Log::info('Hard plant reset executed', [
                'target_user_id' => $targetUser->id,
                'admin_user_id'  => $adminUser->id,
                'reason'         => $reason,
            ]);

            return [
                'success' => true,
                'message' => "Data pohon {$targetUser->nama_lengkap} berhasil direset ke kondisi awal.",
            ];

        } catch (\Throwable $e) {
            Log::error('Hard plant reset FAILED', [
                'target_user_id' => $targetUser->id,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Reset gagal karena kesalahan sistem. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Retrieve the reset history for a given user.
     */
    public function getResetHistory(User $user): \Illuminate\Support\Collection
    {
        return DB::table('plant_resets')
            ->where('target_user_id', $user->id)
            ->join('users as admins', 'admins.id', '=', 'plant_resets.admin_user_id')
            ->select(
                'plant_resets.*',
                'admins.nama_lengkap as admin_name',
                'admins.nik as admin_nik'
            )
            ->orderByDesc('plant_resets.created_at')
            ->get();
    }
}
