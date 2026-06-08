<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tekanan_darah', 'berat_badan', 'tinggi_badan', 
        'konsumsi_garam', 'status_hipertensi', 'keluhan', 'tanggal_input'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_input' => 'date',
            'berat_badan' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}