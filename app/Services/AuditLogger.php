<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Centralized audit logging service.
 *
 * Safe columns only — never exposes password or remember_token
 * when audit logs are retrieved with their user relationship.
 *
 * Wraps writes in try/catch so an audit failure never crashes the
 * primary request. Errors are written to the application log.
 */
class AuditLogger
{
    /**
     * Log an action to the audit_logs table.
     *
     * @param string $action      Short action code, e.g. 'CREATE_USER'.
     * @param string $description Human-readable description.
     * @param int|null $userId    Override for the acting user (defaults to Auth::id()).
     */
    public static function log(string $action, string $description, ?int $userId = null): void
    {
        try {
            AuditLog::create([
                'user_id'     => $userId ?? Auth::id(),
                'action'      => $action,
                'description' => $description,
                'ip_address'  => Request::ip(),
                'route'       => Request::route()?->getName() ?? Request::path(),
                'method'      => Request::method(),
                'user_agent'  => mb_substr((string) Request::userAgent(), 0, 512),
            ]);
        } catch (\Throwable $e) {
            logger()->error('AuditLogger failed: ' . $e->getMessage(), [
                'action'      => $action,
                'description' => $description,
            ]);
        }
    }

    /**
     * Fetch recent logs with SAFE eager-loading (no password/remember_token).
     *
     * Always use this method in controllers instead of
     * AuditLog::with('user') to prevent accidental data exposure.
     *
     * @param int $limit
     * @param string|null $role Only return logs for users of this role (null = all).
     */
    public static function recent(int $limit = 15, ?string $filterOutRole = null): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::select([
                'id', 'user_id', 'action', 'description',
                'ip_address', 'route', 'method', 'created_at',
            ])
            ->with(['user:id,nama_lengkap,email,role'])
            ->when($filterOutRole !== null, fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('role', '!=', $filterOutRole))
            )
            ->latest()
            ->take($limit)
            ->get();
    }
}
