<?php

use App\Models\SiteSetting;
use App\Support\Media;

if (! function_exists('media')) {
    /**
     * Resolve a stored media path to a public URL.
     */
    function media(?string $path): ?string
    {
        return Media::url($path);
    }
}

if (! function_exists('style')) {
    /**
     * Resolve a public asset path to a URL that is cache busted by its last modified time.
     *
     * The layout serves plain CSS files through <link> tags rather than Vite, so without a
     * version query browsers keep serving a stale stylesheet after an edit.
     */
    function style(string $path): string
    {
        $url = asset($path);
        $file = public_path($path);

        return file_exists($file) ? $url.'?v='.filemtime($file) : $url;
    }
}

if (! function_exists('wa_link')) {
    /**
     * Build a wa.me link for a message using the configured WhatsApp number.
     */
    function wa_link(?string $message = null): ?string
    {
        $number = ltrim((string) SiteSetting::current()?->whatsapp_number, '+');

        if ($number === '') {
            return null;
        }

        $link = 'https://wa.me/'.$number;

        return filled($message) ? $link.'?text='.rawurlencode($message) : $link;
    }
}
