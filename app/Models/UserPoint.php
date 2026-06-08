<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'total_points',
        'total_leaves',
        'last_checkin_date',
    ];
}