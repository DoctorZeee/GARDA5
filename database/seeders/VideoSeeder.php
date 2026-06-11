<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
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

        foreach ($videos as $data) {
            Video::firstOrCreate(
                ['youtube_id' => $data['youtube_id']],
                $data
            );
        }
    }
}
