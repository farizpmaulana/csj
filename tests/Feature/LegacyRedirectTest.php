<?php

$catalog = require __DIR__.'/../../config/catalog.php';

it('redirects every legacy page to its new address', function (string $legacyPath) use ($catalog) {
    $target = $catalog['legacy'][$legacyPath];

    $this->get('/'.$legacyPath)
        ->assertMovedPermanently()
        ->assertRedirect(route($target['route'], $target['parameters']));
})->with(array_keys($catalog['legacy']));

it('returns 404 for an unknown legacy path', function () {
    $this->get('/products/does-not-exist.html')->assertNotFound();
});
