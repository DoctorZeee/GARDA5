<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'youtube_id'    => $this->faker->regexify('[A-Za-z0-9_-]{11}'),
            'title'         => $this->faker->sentence(4),
            'points_reward' => $this->faker->numberBetween(1, 5),
            'is_active'     => true,
        ];
    }
}
