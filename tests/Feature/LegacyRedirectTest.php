<?php

use App\Models\LegacyRedirect;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('app:import-catalog');
});

it('redirects every legacy page to its new address', function () {
    $redirects = LegacyRedirect::orderBy('id')->get();

    expect($redirects)->not->toBeEmpty();

    $redirects->each(function (LegacyRedirect $redirect): void {
        $this->get('/'.$redirect->from_path)
            ->assertMovedPermanently()
            ->assertRedirect($redirect->destinationUrl());
    });
});

it('returns 404 for an unknown legacy path', function () {
    $this->get('/products/does-not-exist.html')->assertNotFound();
});
