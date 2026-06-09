<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Catat aktivitas ke tabel audit_logs.
     * FIX: Tambah try-catch agar audit log failure tidak crash request utama.
     */
    public static function log(string $action, string $description): void
    {
        try {
            AuditLog::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'description' => $description,
                'ip_address'  => Request::ip(),
                'route'       => Request::route()?->getName() ?? Request::path(),
                'method'      => Request::method(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('AuditLogger gagal: ' . $e->getMessage(), [
                'action'      => $action,
                'description' => $description,
            ]);
        }
    }
}
