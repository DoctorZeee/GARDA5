<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Catat aktivitas krusial ke tabel audit_logs.
     */
    public static function log(string $action, string $description): void
    {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'route'       => Request::path(),
            'method'      => Request::method(),
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}