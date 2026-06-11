<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $fillable = ['nama_wilayah'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
