<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Development-only fixtures.
 *
 * NEVER called in production. All accounts use predictable passwords
 * for local testing only.
 */
class DevFixtureSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('DevFixtureSeeder TIDAK BOLEH dijalankan di production!');
            return;
        }

        $wilayah = Wilayah::first();

        $staff = [
            [
                'nik'           => '1111111111111111',
                'nama_lengkap'  => 'Super Admin GARDA',
                'email'         => 'admin@garda.dev',
                'role'          => UserRole::Admin->value,
                'jenis_kelamin' => 'L',
            ],
            [
                'nik'           => '2222222222222222',
                'nama_lengkap'  => 'Puskesmas Purwokerto Utara',
                'email'         => 'puskesmas@garda.dev',
                'role'          => UserRole::Puskesmas->value,
                'jenis_kelamin' => 'P',
            ],
            [
                'nik'           => '3333333333333333',
                'nama_lengkap'  => 'Kader Rahmawati',
                'email'         => 'kader@garda.dev',
                'role'          => UserRole::Kader->value,
                'jenis_kelamin' => 'P',
            ],
        ];

        foreach ($staff as $data) {
            if (User::where('nik', $data['nik'])->exists()) {
                continue;
            }

            $role = $data['role'];
            unset($data['role']);

            $user = User::create(array_merge($data, [
                'password'      => Hash::make('password123'),
                'tempat_lahir'  => 'Purwokerto',
                'tanggal_lahir' => '1990-01-01',
                'alamat'        => 'Purwokerto',
                'berat_badan'   => 65,
                'wilayah_id'    => $wilayah?->id,
            ]));

            $user->role = $role;
            $user->save();
        }

        // Dummy warga (warga role) with UserPoint records
        $wilayahs = Wilayah::all();
        for ($i = 0; $i < 5; $i++) {
            $nik = '999999999999' . str_pad($i, 4, '0', STR_PAD_LEFT);

            if (User::where('nik', $nik)->exists()) {
                continue;
            }

            $user = User::create([
                'nik'           => $nik,
                'nama_lengkap'  => 'Warga Demo ' . ($i + 1),
                'email'         => 'warga' . $i . '@garda.dev',
                'password'      => Hash::make('password123'),
                'tempat_lahir'  => 'Purwokerto',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => $i % 2 === 0 ? 'L' : 'P',
                'alamat'        => 'Jl. Demo No. ' . ($i + 1),
                'berat_badan'   => rand(50, 90),
                'wilayah_id'    => $wilayahs->isNotEmpty() ? $wilayahs->random()->id : null,
            ]);

            $user->role = UserRole::User->value;
            $user->save();

            $user->point()->create([
                'total_points'   => rand(10, 50),
                'total_leaves'   => rand(10, 50),
                'checkin_streak' => rand(0, 7),
                'checkin_count'  => rand(5, 30),
            ]);
        }

        $this->command->info('✅ Dev fixtures seeded. Gunakan password: password123');
    }
}
