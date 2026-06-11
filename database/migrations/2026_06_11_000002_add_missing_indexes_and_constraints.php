<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Production hardening: add missing database constraints and indexes.
 *
 * - Unique constraint on videos.youtube_id (prevent duplicate videos)
 * - Index on health_logs (user_id, tanggal_input) for fast duplicate-check queries
 * - Index on audit_logs (action) for filtered queries
 * - Index on user_points.last_checkin_date for streak queries
 */
return new class extends Migration
{
    public function up(): void
    {
        // Unique youtube_id — only add if not already present
        if (Schema::hasTable('videos')) {
            Schema::table('videos', function (Blueprint $table) {
                // The unique index may already exist from an earlier migration; use try/catch in case.
                try {
                    $table->unique('youtube_id', 'videos_youtube_id_unique');
                } catch (\Exception $e) {
                    // Already exists — skip
                }
            });
        }

        if (Schema::hasTable('health_logs')) {
            Schema::table('health_logs', function (Blueprint $table) {
                // Composite index for duplicate-day check: WHERE user_id = ? AND tanggal_input = ?
                try {
                    $table->index(['user_id', 'tanggal_input'], 'health_logs_user_date_idx');
                } catch (\Exception $e) {
                    // Already exists — skip
                }
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                try {
                    $table->index('action', 'audit_logs_action_idx');
                } catch (\Exception $e) {
                    // Already exists — skip
                }
            });
        }

        if (Schema::hasTable('user_points')) {
            Schema::table('user_points', function (Blueprint $table) {
                try {
                    $table->index('last_checkin_date', 'user_points_last_checkin_idx');
                } catch (\Exception $e) {
                    // Already exists — skip
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('videos',      fn ($t) => $t->dropUnique('videos_youtube_id_unique'));
        Schema::table('health_logs', fn ($t) => $t->dropIndex('health_logs_user_date_idx'));
        Schema::table('audit_logs',  fn ($t) => $t->dropIndex('audit_logs_action_idx'));
        Schema::table('user_points', fn ($t) => $t->dropIndex('user_points_last_checkin_idx'));
    }
};
