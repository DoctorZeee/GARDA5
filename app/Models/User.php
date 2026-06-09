<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 'role' sengaja TIDAK ada di $fillable.
     * Mencegah privilege escalation jika di masa depan ada
     * $user->update($request->validated()) tanpa membuang field role.
     * Perubahan role harus dilakukan secara eksplisit:
     *   $user->role = $request->role;
     *   $user->save();
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

    public function getUmurAttribute(): ?int
    {
        if (empty($this->attributes['tanggal_lahir'])) {
            return null;
        }
        return Carbon::parse($this->attributes['tanggal_lahir'])->age;
    }

    public function getNameAttribute(): string
    {
        return $this->nama_lengkap ?? '';
    }

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
}
