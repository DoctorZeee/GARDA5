<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Injects security headers and generates a per-request CSP nonce.
 *
 * Nonce is accessible in Blade via csp_nonce() helper (defined in helpers.php).
 * Vite::useCspNonce() ensures compiled assets are also allowlisted.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generate nonce BEFORE calling $next() so it's available during view rendering
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        Vite::useCspNonce($nonce);

        $response = $next($request);

        if (! ($response instanceof Response)) {
            return $response;
        }

        $isProduction = app()->environment('production');

        // Development: allow Vite dev server
        $viteDevSrc = ! $isProduction
            ? ' http://127.0.0.1:5173 http://127.0.0.1:5174 ws://127.0.0.1:5173 ws://127.0.0.1:5174'
            : '';

        $scriptSrc = "'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://unpkg.com https://code.jquery.com https://cdn.datatables.net" . $viteDevSrc;
        $styleSrc  = "'self' 'nonce-{$nonce}' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net https://cdn.datatables.net" . $viteDevSrc;

        if (! $isProduction) {
            // unsafe-eval needed by Vite HMR in dev
            $scriptSrc .= " 'unsafe-eval'";
        }

        // img-src: restricted to self, data URIs (base64 avatars), and trusted CDNs only.
        // YouTube thumbnail domain is explicitly allowed for video thumbnails.
        $imgSrc = "'self' data: https://img.youtube.com https://i.ytimg.com https://fonts.gstatic.com";

        $csp = implode(' ', [
            "default-src 'self';",
            "script-src {$scriptSrc};",
            "style-src {$styleSrc};",
            "style-src-attr 'unsafe-inline';",   // Inline style attributes (Bootstrap utilities)
            "img-src {$imgSrc};",
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com;",
            "frame-src https://www.youtube.com https://www.youtube-nocookie.com;",
            "connect-src 'self'{$viteDevSrc};",
            "frame-ancestors 'none';",
            "base-uri 'self';",
            "form-action 'self';",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($isProduction) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
