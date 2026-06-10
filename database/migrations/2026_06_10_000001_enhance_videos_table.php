<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan kolom yang dibutuhkan agar video CRUD lengkap:
 *  - description : deskripsi singkat video (opsional)
 *  - sort_order  : urutan tampil di dashboard (default 0 = urutan insert)
 * Kolom lama (youtube_id, title, points_reward, is_active) tetap utuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['description', 'sort_order']);
        });
    }
};
