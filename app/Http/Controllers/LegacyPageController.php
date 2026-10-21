<?php

namespace App\Http\Controllers;

use App\Models\LegacyRedirect;
use Illuminate\Http\RedirectResponse;

class LegacyPageController extends Controller
{
    public function __invoke(string $path): RedirectResponse
    {
        // Legacy paths contain dots, so the lookup is a plain equality match
        // rather than a path segment match.
        $redirect = LegacyRedirect::query()
            ->where('from_path', $path)
            ->firstOrFail();

        return redirect()->to($redirect->destinationUrl(), 301);
    }
}
