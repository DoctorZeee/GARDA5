<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Wilayah
        $w1 = Wilayah::firstOrCreate(['nama_wilayah' => 'Bancarkembar']);
        $w2 = Wilayah::firstOrCreate(['nama_wilayah' => 'Grendeng']);
        $w3 = Wilayah::firstOrCreate(['nama_wilayah' => 'Karangwangkal']);
        $wilayahs = [$w1, $w2, $w3];

        // 2. Master Video (idempotent via firstOrCreate pada youtube_id)
        $videosData = [
            [
                'youtube_id'    => 'q7V68m7i86s',
                'title'         => 'Resep Dapur Sehat Rendah Natrium',
                'description'   => 'Cara memasak lezat tanpa banyak garam untuk menjaga tekanan darah.',
                'points_reward' => 1,
                'is_active'     => true,
                'sort_order'    => 1,
            ],
            [
                'youtube_id'    => 'K-6Z6E8PAnk',
                'title'         => 'Senam Anti Hipertensi Komunitas',
                'description'   => 'Gerakan senam ringan yang terbukti membantu menurunkan tekanan darah tinggi.',
                'points_reward' => 1,
                'is_active'     => true,
                'sort_order'    => 2,
            ],
        ];

        foreach ($videosData as $vd) {
            Video::firstOrCreate(['youtube_id' => $vd['youtube_id']], $vd);
        }

        // 3. Core Accounts
        $usersData = [
            [
                'nik' => '1111111111111111', 'nama_lengkap' => 'Super Admin GARDA',
                'email' => 'admin@garda.com', 'role' => 'admin',
                'password' => Hash::make('password123'),
                'tempat_lahir' => 'Purwokerto', 'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'L', 'alamat' => 'UMP', 'berat_badan' => 70,
                'wilayah_id' => $w1->id,
            ],
            [
                'nik' => '2222222222222222', 'nama_lengkap' => 'Puskesmas Purwokerto Utara',
                'email' => 'puskesmas@garda.com', 'role' => 'puskesmas',
                'password' => Hash::make('password123'),
                'tempat_lahir' => 'Purwokerto', 'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'P', 'alamat' => 'Puskesmas', 'berat_badan' => 60,
                'wilayah_id' => $w1->id,
            ],
            [
                'nik' => '3333333333333333', 'nama_lengkap' => 'Kader Rahmawati',
                'email' => 'kader@garda.com', 'role' => 'kader',
                'password' => Hash::make('password123'),
                'tempat_lahir' => 'Purwokerto', 'tanggal_lahir' => '1995-05-05',
                'jenis_kelamin' => 'P', 'alamat' => 'Bancarkembar', 'berat_badan' => 55,
                'wilayah_id' => $w1->id,
            ],
        ];

        foreach ($usersData as $data) {
            if (! User::where('nik', $data['nik'])->exists()) {
                User::create($data);
            }
        }

        // 4. Dummy Users + UserPoint (termasuk kolom streak baru)
        for ($i = 0; $i < 5; $i++) {
            $nik = '999999999999' . str_pad($i, 4, '0', STR_PAD_LEFT);
            if (User::where('nik', $nik)->exists()) {
                continue;
            }

            $user = User::create([
                'nik'           => $nik,
                'nama_lengkap'  => 'Warga ' . ($i + 1),
                'email'         => 'warga' . $i . '@garda.com',
                'password'      => Hash::make('password123'),
                'role'          => 'user',
                'tempat_lahir'  => 'Purwokerto',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'L',
                'alamat'        => 'Alamat Warga ' . ($i + 1),
                'berat_badan'   => rand(50, 80),
                'wilayah_id'    => $wilayahs[array_rand($wilayahs)]->id,
            ]);

            $user->point()->create([
                'total_points'   => rand(10, 50),
                'total_leaves'   => rand(10, 50),
                'checkin_streak' => 0,
                'checkin_count'  => rand(5, 30),
            ]);
        }
    }
}
