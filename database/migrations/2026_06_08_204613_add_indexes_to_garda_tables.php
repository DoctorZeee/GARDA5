<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan metode helper kustom agar mengabaikan error Duplicate Key
        $this->addIndexSafe('users', 'wilayah_id');
        $this->addIndexSafe('users', 'role');
        
        $this->addIndexSafe('health_logs', 'user_id');
        $this->addIndexSafe('health_logs', 'created_at');
        
        $this->addIndexSafe('user_points', 'user_id');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['wilayah_id']);
            $table->dropIndex(['role']);
        });

        Schema::table('health_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('user_points', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }

    /**
     * Helper untuk menambahkan index dengan aman (Fail-Safe)
     */
    private function addIndexSafe(string $tableName, string $columnName): void
    {
        try {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->index($columnName);
            });
        } catch (\Exception $e) {
            // Jika masuk ke sini, berarti index sudah ada di database.
            // Kita abaikan (skip) agar proses migrasi tidak terhenti/error.
        }
    }
};