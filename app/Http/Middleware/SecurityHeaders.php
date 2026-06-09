<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!($response instanceof Response)) {
            return $response;
        }

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // HSTS hanya untuk HTTPS (production). Di local dev HTTP ini tidak berbahaya
        // tapi bisa diabaikan browser. Cukup set saat APP_ENV=production.
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP: bedakan dev (Vite HMR) vs production
        $viteDevSrc = app()->environment('local')
            ? ' http://127.0.0.1:5173 http://127.0.0.1:5174 ws://127.0.0.1:5173 ws://127.0.0.1:5174'
            : '';

        $csp = "default-src 'self';" .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://code.jquery.com https://cdn.datatables.net{$viteDevSrc};" .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net https://cdn.datatables.net{$viteDevSrc};" .
               "img-src 'self' data: https: http:;" .
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com;" .
               "frame-src https://www.youtube.com;" .
               "connect-src 'self'{$viteDevSrc};";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
