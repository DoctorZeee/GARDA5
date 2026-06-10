<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom checkin_streak dan checkin_count ke tabel user_points.
 *  - checkin_streak : streak beruntun (berguna untuk sistem reward streak di masa depan)
 *  - checkin_count  : total kumulatif check-in sepanjang waktu
 *
 * TIDAK mengubah kolom yang sudah ada agar zero-downtime.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            $table->unsignedInteger('checkin_streak')->default(0)->after('last_checkin_date');
            $table->unsignedInteger('checkin_count')->default(0)->after('checkin_streak');
        });
    }

    public function down(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            $table->dropColumn(['checkin_streak', 'checkin_count']);
        });
    }
};
