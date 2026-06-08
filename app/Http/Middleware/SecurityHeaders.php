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

        if ($response instanceof Response) {
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            
            // CSP yang diperbarui agar menerima semua CDN yang digunakan
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com http://127.0.0.1:5174; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net http://127.0.0.1:5174; " .
                   "img-src 'self' data: https: http:; " .
                   "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
                   "connect-src 'self' ws://127.0.0.1:5174 http://127.0.0.1:5174;";

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}