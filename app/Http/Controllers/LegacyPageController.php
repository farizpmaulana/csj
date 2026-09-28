<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LegacyPageController extends Controller
{
    public function __invoke(string $path): RedirectResponse
    {
        $legacy = config('catalog.legacy');

        // Legacy paths contain dots, so they cannot be read through config()'s
        // dot notation and must be looked up as plain array keys.
        abort_unless(array_key_exists($path, $legacy), 404);

        return redirect()->route($legacy[$path]['route'], $legacy[$path]['parameters'], 301);
    }
}
