<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * FIX: Support multi-role → role:admin,puskesmas
     * Sebelumnya hanya strict equality satu role.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $allowed = collect($roles)
            ->flatMap(fn ($r) => explode(',', $r))
            ->map(fn ($r) => trim($r))
            ->filter()
            ->toArray();

        if (! in_array(Auth::user()->role, $allowed, strict: true)) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin mengakses halaman ini.');
        }

        return $next($request);
    }
}
