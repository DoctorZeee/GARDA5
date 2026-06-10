<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'youtube_id',
        'title',
        'description',
        'points_reward',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active'     => 'boolean',
            'points_reward' => 'integer',
            'sort_order'    => 'integer',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    public function claims(): HasMany
    {
        return $this->hasMany(UserVideoClaim::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────

    /**
     * Hanya video aktif, diurutkan sesuai sort_order lalu created_at.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('created_at');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * URL embed YouTube yang aman.
     */
    public function getEmbedUrlAttribute(): string
    {
        return 'https://www.youtube.com/embed/' . e($this->youtube_id);
    }

    /**
     * URL thumbnail YouTube (hqdefault 480×360).
     */
    public function getThumbnailUrlAttribute(): string
    {
        return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }
}
