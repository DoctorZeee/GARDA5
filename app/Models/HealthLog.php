<?php

namespace App\Models;

use App\ValueObjects\BloodPressure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'tekanan_darah', 'berat_badan', 'tinggi_badan',
        'konsumsi_garam', 'status_hipertensi', 'keluhan', 'tanggal_input',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_input' => 'date',
            'berat_badan'   => 'decimal:2',
        ];
    }

    // ─── Value Object Accessor ────────────────────────────────────────────────

    /**
     * Returns typed BloodPressure value object, or null if not set / invalid.
     */
    public function getBloodPressureAttribute(): ?BloodPressure
    {
        return BloodPressure::tryFromString($this->tekanan_darah);
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
