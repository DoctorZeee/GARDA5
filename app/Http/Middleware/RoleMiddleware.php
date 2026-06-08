<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            // Jika mencoba akses route role lain, lempar ke 403 Forbidden
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin mengakses halaman ini.');
        }

        return $next($request);
    }
}