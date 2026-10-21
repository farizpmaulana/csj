<?php

use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('app:import-catalog');
});

it('renders the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('PT Cahaya Sarange Jaya', escape: false)
        ->assertSee(route('articles.index'), escape: false)
        ->assertDontSee('assets/css/');
});

it('lists every brand as a partner card on the home page', function () {
    $response = $this->get(route('home'))->assertOk();

    Brand::visible()->ordered()->get()->each(function (Brand $brand) use ($response): void {
        $response->assertSee(route('products.brand', ['brand' => $brand->slug]), escape: false);
    });
});

it('renders a catalog page for every brand', function () {
    $brands = Brand::visible()->ordered()->get();

    expect($brands)->not->toBeEmpty();

    $brands->each(function (Brand $brand): void {
        $this->get(route('products.brand', ['brand' => $brand->slug]))
            ->assertOk()
            ->assertSee($brand->name)
            ->assertSee($brand->intro, escape: false);
    });
});

it('renders a product page for every product', function () {
    $products = Product::visible()->with('brand')->orderBy('id')->get();

    expect($products)->not->toBeEmpty();

    $products->each(function (Product $product): void {
        $this->get(route('products.show', [
            'brand' => $product->brand->slug,
            'product' => $product->slug,
        ]))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($product->description, escape: false)
            ->assertSee(route('products.brand', ['brand' => $product->brand->slug]), escape: false);
    });
});

it('renders the article index listing every article', function () {
    $response = $this->get(route('articles.index'))->assertOk();

    Article::published()->get()->each(function (Article $article) use ($response): void {
        $response->assertSee(route('articles.show', ['article' => $article->slug]), escape: false);
    });
});

it('renders an article page for every article', function () {
    $articles = Article::published()->orderBy('id')->get();

    expect($articles)->not->toBeEmpty();

    $articles->each(function (Article $article): void {
        $this->get(route('articles.show', ['article' => $article->slug]))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee($article->hero_alt, escape: false);
    });
});

it('hides unpublished articles from the public', function () {
    $article = Article::first();
    $article->update(['is_published' => false]);

    $this->get(route('articles.show', ['article' => $article->slug]))->assertNotFound();
    $this->get(route('articles.index'))->assertOk()->assertDontSee($article->title);
});

it('hides invisible brands and products from the public', function () {
    $brand = Brand::first();
    $product = Product::first();
    $brand->update(['is_visible' => false]);
    $product->update(['is_visible' => false]);

    $this->get(route('products.brand', ['brand' => $brand->slug]))->assertNotFound();
    $this->get(route('products.show', ['brand' => $product->brand->slug, 'product' => $product->slug]))
        ->assertNotFound();
});

it('hides hidden clients and gallery images from the home page', function () {
    $client = Client::first();
    $gallery = GalleryImage::first();
    $client->update(['is_visible' => false]);
    $gallery->update(['is_visible' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee(media($client->path))
        ->assertDontSee(media($gallery->path));
});

it('resolves every asset path referenced by a page to a real file', function () {
    $pages = [
        route('home'),
        route('articles.index'),
        ...Brand::visible()->ordered()->get()
            ->map(fn (Brand $brand) => route('products.brand', ['brand' => $brand->slug]))->all(),
        ...Product::visible()->with('brand')->orderBy('id')->get()
            ->map(fn (Product $product) => route('products.show', [
                'brand' => $product->brand->slug,
                'product' => $product->slug,
            ]))->all(),
        ...Article::published()->orderBy('id')->get()
            ->map(fn (Article $article) => route('articles.show', ['article' => $article->slug]))->all(),
    ];

    foreach ($pages as $page) {
        $html = $this->get($page)->getContent();

        preg_match_all('~(?:src|href|data-[\w-]+)="([^"]+)"~', $html, $matches);

        foreach ($matches[1] as $url) {
            $path = parse_url($url, PHP_URL_PATH);

            // Uploaded media lives on the public disk rather than in public/.
            if (str_starts_with($url, asset('storage/'))) {
                expect(storage_path('app/public/'.ltrim(substr($path, strlen('/storage/')), '/')))
                    ->toBeFile();

                continue;
            }

            if (! str_starts_with($url, asset('')) || ! preg_match('/\.[a-z0-9]+$/i', $path)) {
                continue;
            }

            expect(public_path(ltrim($path, '/')))->toBeFile();
        }
    }
});

it('lists a product that is not filed under any category', function () {
    $brand = Brand::query()->create(['name' => 'Makita Indonesia', 'slug' => 'makita', 'position' => 9]);

    $product = Product::query()->create([
        'brand_id' => $brand->getKey(),
        'product_category_id' => null,
        'name' => 'Makita Hammer',
        'slug' => 'makita-hammer',
        'is_visible' => true,
    ]);

    $this->get(route('products.brand', ['brand' => 'makita']))
        ->assertOk()
        ->assertSee('Makita Hammer')
        ->assertSee(route('products.show', ['brand' => 'makita', 'product' => 'makita-hammer']), escape: false);

    expect($brand->uncategorisedProducts->modelKeys())->toBe([$product->getKey()]);
});

it('cache busts page stylesheets so a css edit is never served stale', function () {
    $html = $this->get(route('products.show', ['brand' => 'sumato', 'product' => 'sumato-sm-05']))->getContent();

    preg_match_all('~<link rel="stylesheet" href="([^"]+)"~', $html, $matches);

    $local = array_values(array_filter(
        $matches[1],
        fn (string $url) => str_starts_with($url, asset('')) && str_ends_with(parse_url($url, PHP_URL_PATH), '.css'),
    ));

    expect($local)->not->toBeEmpty();

    foreach ($local as $url) {
        $path = ltrim(parse_url($url, PHP_URL_PATH), '/');

        expect(parse_url($url, PHP_URL_QUERY))->toBe('v='.filemtime(public_path($path)));
    }
});

it('renders a rich text product description as html and keeps the meta description plain text', function () {
    $brand = Brand::query()->create(['name' => 'Makita Indonesia', 'slug' => 'makita', 'position' => 9]);

    $product = Product::query()->create([
        'brand_id' => $brand->getKey(),
        'product_category_id' => null,
        'name' => 'Makita Hammer',
        'slug' => 'makita-hammer',
        'description' => '<p>Alat paku <strong>bertenaga</strong> dengan garansi resmi.</p>',
        'is_visible' => true,
    ]);

    $response = $this->get(route('products.show', ['brand' => 'makita', 'product' => 'makita-hammer']));

    $response->assertOk()
        ->assertSee('<div class="detail-description"><p>Alat paku <strong>bertenaga</strong> dengan garansi resmi.</p></div>', escape: false)
        ->assertDontSee('&lt;p&gt;Alat paku', escape: false);

    expect($response->getContent())
        ->toContain('<meta name="description" content="Alat paku bertenaga dengan garansi resmi." />')
        ->toContain('<meta property="og:description" content="Alat paku bertenaga dengan garansi resmi." />');
});

it('omits the product description card when a product has no description', function () {
    $product = Product::where('slug', 'sumato-sm-05')->firstOrFail();
    $product->forceFill(['description' => null])->save();

    $this->get(route('products.show', ['brand' => $product->brand->slug, 'product' => $product->slug]))
        ->assertOk()
        ->assertDontSee('detail-description', escape: false);
});

it('returns 404 for an unknown brand', function () {
    $this->get('/products/nonexistent')->assertNotFound();
});
it('returns 404 for an unknown product', function () {
    $this->get('/products/bosch/nonexistent')->assertNotFound();
});

it('returns 404 for a product requested under the wrong brand', function () {
    $product = Product::where('slug', 'sumato-sm-05')->firstOrFail();

    $this->get('/products/bosch/'.$product->slug)->assertNotFound();
});

it('returns 404 for an unknown article', function () {
    $this->get('/articles/nonexistent')->assertNotFound();
});
