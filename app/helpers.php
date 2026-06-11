<?php

if (! function_exists('csp_nonce')) {
    /**
     * Return the per-request CSP nonce string, or empty string if not set.
     * Safe to call on pages that bypass SecurityHeaders middleware.
     */
    function csp_nonce(): string
    {
        return request()->attributes->get('csp_nonce', '');
    }
}

if (! function_exists('html_clean')) {
    /**
     * Sanitize a user-supplied HTML string, stripping dangerous tags/attrs.
     *
     * Use for content that should allow basic formatting (bold, italic, links)
     * but never script/iframe/onclick.
     *
     * Falls back to e() (full escaping) if HTMLPurifier is not installed.
     *
     * @param string|null $value Raw user input.
     * @return string Sanitized HTML string.
     */
    function html_clean(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // If HTMLPurifier is available, use it for selective allow-listing
        if (class_exists(\HTMLPurifier::class)) {
            static $purifier;
            if ($purifier === null) {
                $config = \HTMLPurifier_Config::createDefault();
                $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,ol,ul,li,a[href|title]');
                $config->set('HTML.Nofollow', true);
                $config->set('HTML.TargetBlank', false);
                $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);
                $config->set('Cache.SerializerPath', storage_path('framework/cache'));
                $purifier = new \HTMLPurifier($config);
            }
            return $purifier->purify($value);
        }

        // Fallback: full escaping if HTMLPurifier not available
        return e($value);
    }
}
