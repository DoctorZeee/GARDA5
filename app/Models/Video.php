<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

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

    // ─── Relations ───────────────────────────────────────────────────────────

    public function claims(): HasMany
    {
        return $this->hasMany(UserVideoClaim::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Active videos ordered by sort_order then created_at.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->orderBy('sort_order')
                     ->orderBy('created_at');
    }

    // ─── Computed Attributes ─────────────────────────────────────────────────

    /**
     * Safe YouTube embed URL.
     */
    public function getEmbedUrlAttribute(): string
    {
        return 'https://www.youtube.com/embed/' . e($this->youtube_id);
    }

    /**
     * YouTube hqdefault thumbnail (480×360).
     */
    public function getThumbnailUrlAttribute(): string
    {
        return 'https://img.youtube.com/vi/' . e($this->youtube_id) . '/hqdefault.jpg';
    }
}
