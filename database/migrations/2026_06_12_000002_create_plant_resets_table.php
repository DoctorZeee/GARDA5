<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks every hard reset (manual admin-triggered) of a user's plant.
 * Distinct from monthly_point_snapshots (automatic schedule).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plant_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 1000)->nullable();

            // Snapshot of values BEFORE reset
            $table->unsignedInteger('before_total_points')->default(0);
            $table->unsignedInteger('before_total_leaves')->default(0);
            $table->unsignedInteger('before_checkin_streak')->default(0);
            $table->unsignedInteger('before_checkin_count')->default(0);

            $table->timestamps();

            $table->index('target_user_id');
            $table->index('admin_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_resets');
    }
};
