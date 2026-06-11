<?php

namespace App\Models;

use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * 'role' is intentionally excluded from $fillable.
     *
     * Role changes must be explicit:
     *   $user->role = UserRole::Admin->value;
     *   $user->save();
     *
     * This prevents privilege escalation via mass assignment.
     */
    protected $fillable = [
        'nik', 'nama_lengkap', 'email', 'password',
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'alamat', 'berat_badan', 'tekanan_darah', 'wilayah_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['umur'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'tanggal_lahir'     => 'date',
            'berat_badan'       => 'decimal:2',
        ];
    }

    // ─── Computed Attributes ─────────────────────────────────────────────────

    public function getUmurAttribute(): ?int
    {
        if (empty($this->attributes['tanggal_lahir'])) {
            return null;
        }

        return Carbon::parse($this->attributes['tanggal_lahir'])->age;
    }

    /**
     * Alias for compatibility with components expecting ->name.
     */
    public function getNameAttribute(): string
    {
        return $this->nama_lengkap ?? '';
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin->value;
    }

    public function isPuskesmas(): bool
    {
        return $this->role === UserRole::Puskesmas->value;
    }

    public function isKader(): bool
    {
        return $this->role === UserRole::Kader->value;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::User->value;
    }

    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFrom($this->role);
    }

    public function roleLabel(): string
    {
        return $this->roleEnum()?->label() ?? $this->role;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeWarga($query)
    {
        return $query->where('role', UserRole::User->value);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeInWilayah($query, int $wilayahId)
    {
        return $query->where('wilayah_id', $wilayahId);
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function healthLogs(): HasMany
    {
        return $this->hasMany(HealthLog::class);
    }

    public function point(): HasOne
    {
        return $this->hasOne(UserPoint::class);
    }

    public function videoClaims(): HasMany
    {
        return $this->hasMany(UserVideoClaim::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
