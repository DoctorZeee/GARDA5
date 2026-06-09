<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generate nonce SEBELUM $next() agar tersedia saat view dirender
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        // Sinkronkan nonce ke Vite — harus string, bukan Closure
        Vite::useCspNonce($nonce);

        $response = $next($request);

        if (!($response instanceof Response)) {
            return $response;
        }

        $isProduction = app()->environment('production');

        $viteDevSrc = ! $isProduction
            ? ' http://127.0.0.1:5173 http://127.0.0.1:5174 ws://127.0.0.1:5173 ws://127.0.0.1:5174'
            : '';

        $scriptSrc = "'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://unpkg.com https://code.jquery.com https://cdn.datatables.net" . $viteDevSrc;
        $styleSrc  = "'self' 'nonce-{$nonce}' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net https://cdn.datatables.net" . $viteDevSrc;

        if (! $isProduction) {
            $scriptSrc .= " 'unsafe-eval'";
        }

        $csp = "default-src 'self';" .
               "script-src {$scriptSrc};" .
               "style-src {$styleSrc};" .
               "style-src-attr 'unsafe-inline';" .
               "img-src 'self' data: https: http:;" .
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com;" .
               "frame-src https://www.youtube.com;" .
               "connect-src 'self'{$viteDevSrc};" .
               "frame-ancestors 'none';" .
               "base-uri 'self';" .
               "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($isProduction) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
