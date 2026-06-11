<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add soft deletes (deleted_at) to core tables.
 *
 * Prevents permanent data loss on accidental deletions and
 * preserves audit trail integrity.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('health_logs', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('health_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
