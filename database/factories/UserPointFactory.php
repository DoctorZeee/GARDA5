<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPointFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'total_points'     => 0,
            'total_leaves'     => 0,
            'last_checkin_date' => null,
        ];
    }
}
