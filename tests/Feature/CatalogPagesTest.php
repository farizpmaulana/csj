<?php

$catalog = require __DIR__.'/../../config/catalog.php';
$articles = require __DIR__.'/../../config/articles.php';

it('renders the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('PT Cahaya Sarange Jaya', escape: false)
        ->assertSee(route('articles.index'), escape: false)
        ->assertDontSee('assets/css/');
});

it('renders a catalog page for every brand', function (string $brand) use ($catalog) {
    $this->get(route('products.brand', ['brand' => $brand]))
        ->assertOk()
        ->assertSee($catalog['brands'][$brand]['name']);
})->with(array_keys($catalog['brands']));

it('renders a product page for every product', function (string $product) use ($catalog) {
    $brand = $catalog['products'][$product]['brand'];

    $this->get(route('products.show', ['brand' => $brand, 'product' => $product]))
        ->assertOk()
        ->assertSee($catalog['products'][$product]['name'])
        ->assertSee(route('products.brand', ['brand' => $brand]), escape: false);
})->with(array_keys($catalog['products']));

it('renders the article index listing every article', function () use ($articles) {
    $response = $this->get(route('articles.index'))->assertOk();

    foreach (array_keys($articles) as $slug) {
        $response->assertSee(route('articles.show', ['article' => $slug]), escape: false);
    }
});

it('renders an article page for every article', function (string $article) use ($articles) {
    $this->get(route('articles.show', ['article' => $article]))
        ->assertOk()
        ->assertSee($articles[$article]['title'])
        ->assertSee($articles[$article]['hero_alt'], escape: false);
})->with(array_keys($articles));

it('resolves every asset path referenced by a page to a file in the public directory', function () use ($catalog) {
    $pages = [
        route('home'),
        route('articles.index'),
        ...array_map(
            fn (string $brand) => route('products.brand', ['brand' => $brand]),
            array_keys($catalog['brands'])
        ),
        ...array_map(
            fn (string $product) => route('products.show', [
                'brand' => $catalog['products'][$product]['brand'],
                'product' => $product,
            ]),
            array_keys($catalog['products'])
        ),
        ...array_map(
            fn (string $article) => route('articles.show', ['article' => $article]),
            array_keys($articles = require __DIR__.'/../../config/articles.php')
        ),
    ];

    foreach ($pages as $page) {
        $html = $this->get($page)->getContent();

        preg_match_all('~(?:src|href|data-[\w-]+)="([^"]+)"~', $html, $matches);

        foreach ($matches[1] as $url) {
            $path = parse_url($url, PHP_URL_PATH);

            if (! str_starts_with($url, asset('')) || ! preg_match('/\.[a-z0-9]+$/i', $path)) {
                continue;
            }

            expect(public_path(ltrim($path, '/')))->toBeFile();
        }
    }
});

it('returns 404 for an unknown brand', function () {
    $this->get('/products/nonexistent')->assertNotFound();
});

it('returns 404 for an unknown product', function () {
    $this->get('/products/bosch/nonexistent')->assertNotFound();
});

it('returns 404 for a product requested under the wrong brand', function () {
    $this->get('/products/bosch/sumato-sm-05')->assertNotFound();
});

it('returns 404 for an unknown article', function () {
    $this->get('/articles/nonexistent')->assertNotFound();
});
