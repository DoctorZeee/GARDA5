<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama_lengkap' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password123'),
            'role' => 'user',
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'alamat' => $this->faker->address(),
            'berat_badan' => $this->faker->randomFloat(2, 45, 95),
            'tekanan_darah' => $this->faker->numberBetween(110, 160) . '/' . $this->faker->numberBetween(70, 100),
            'remember_token' => Str::random(10),
        ];
    }
}