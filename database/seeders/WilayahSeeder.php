<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayahs = [
            'Bancarkembar',
            'Grendeng',
            'Karangwangkal',
        ];

        foreach ($wilayahs as $nama) {
            Wilayah::firstOrCreate(['nama_wilayah' => $nama]);
        }
    }
}
