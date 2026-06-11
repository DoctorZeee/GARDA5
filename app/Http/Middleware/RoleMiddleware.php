<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based access middleware.
 *
 * Supports single and multi-role syntax:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,puskesmas')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $r) => explode(',', $r))
            ->map(fn (string $r) => trim($r))
            ->filter()
            ->toArray();

        // Validate that all provided roles are known enum values
        $knownRoles = array_column(UserRole::cases(), 'value');
        $unknownRoles = array_diff($allowed, $knownRoles);
        if (! empty($unknownRoles)) {
            abort(500, 'Unknown role(s) in middleware: ' . implode(', ', $unknownRoles));
        }

        if (! in_array(Auth::user()->role, $allowed, strict: true)) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin mengakses halaman ini.');
        }

        return $next($request);
    }
}
