<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'total_leaves',
        'last_checkin_date',
        'checkin_streak',
        'checkin_count',
    ];

    protected function casts(): array
    {
        return [
            'last_checkin_date' => 'date',
            'total_points'      => 'integer',
            'total_leaves'      => 'integer',
            'checkin_streak'    => 'integer',
            'checkin_count'     => 'integer',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Business Logic ─────────────────────────────────────────────────────

    /**
     * Cek apakah user sudah check-in hari ini.
     */
    public function hasCheckedInToday(): bool
    {
        return $this->last_checkin_date?->toDateString() === Carbon::today()->toDateString();
    }

    /**
     * Proses check-in: update poin, streak, dan tanggal.
     * Dipanggil hanya setelah lock FOR UPDATE di dalam transaksi.
     */
    public function applyCheckin(): void
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday()->toDateString();

        // Streak lanjut hanya jika kemarin check-in, reset jika lewat sehari
        $newStreak = $this->last_checkin_date?->toDateString() === $yesterday
            ? $this->checkin_streak + 1
            : 1;

        $this->update([
            'last_checkin_date' => $today,
            'total_points'      => $this->total_points + 1,
            'total_leaves'      => $this->total_leaves + 1,
            'checkin_streak'    => $newStreak,
            'checkin_count'     => $this->checkin_count + 1,
        ]);
    }
}
