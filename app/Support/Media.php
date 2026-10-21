<?php

namespace App\Support;

class Media
{
    /**
     * Disk that holds every uploaded and seeded image.
     */
    public const DISK = 'public';

    /**
     * The public path the disk is symlinked to, see "php artisan storage:link".
     */
    public const URL_PREFIX = 'storage';

    /**
     * Resolve a stored media path to a public URL.
     *
     * Absolute URLs are passed through so external sources keep working.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return asset(self::URL_PREFIX.'/'.ltrim($path, '/'));
    }

    /**
     * Build a srcset attribute from stored responsive candidates.
     *
     * @param  array<int, array{path: string, width?: int}>|null  $candidates
     */
    public static function srcset(?array $candidates): ?string
    {
        if (blank($candidates)) {
            return null;
        }

        $entries = [];

        foreach ($candidates as $candidate) {
            $url = static::url($candidate['path'] ?? null);

            if ($url === null) {
                continue;
            }

            $entries[] = isset($candidate['width']) && $candidate['width'] > 0
                ? $url.' '.$candidate['width'].'w'
                : $url;
        }

        return $entries === [] ? null : implode(', ', $entries);
    }
}
