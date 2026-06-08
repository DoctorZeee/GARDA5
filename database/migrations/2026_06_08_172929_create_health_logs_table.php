<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('health_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tekanan_darah')->nullable();
            $table->decimal('berat_badan', 5, 2); // kg
            $table->integer('tinggi_badan'); // cm
            $table->enum('konsumsi_garam', ['less', 'ideal', 'more']);
            $table->enum('status_hipertensi', ['Normal', 'Ringan', 'Sedang', 'Berat'])->index();
            $table->text('keluhan')->nullable();
            $table->date('tanggal_input')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_logs');
    }
};