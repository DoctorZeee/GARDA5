<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores a monthly snapshot of user point data before the
 * automatic reset. Provides full historical record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_point_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('month', 7);          // Format: "2026-06"
            $table->unsignedInteger('total_points');
            $table->unsignedInteger('total_leaves');
            $table->unsignedInteger('checkin_streak');
            $table->unsignedInteger('checkin_count');
            $table->timestamps();

            // One snapshot per user per month
            $table->unique(['user_id', 'month'], 'snapshots_user_month_unique');
            $table->index('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_point_snapshots');
    }
};
