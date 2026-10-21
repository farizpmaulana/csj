<?php

use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\LegacyRedirect;
use App\Models\Product;
use App\Models\ProductSectionRow;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Artisan::call('app:import-catalog');
});

/**
 * Every media path stored in the database must still exist on the public disk.
 */
function assertMediaExists(?string $path): void
{
    if ($path === null || $path === '') {
        return;
    }

    expect(is_file(storage_path('app/public/'.$path)))
        ->toBeTrue("Missing media file for path [{$path}].");
}

it('imports the seeded catalog', function () {
    expect(Brand::count())->toBe(5)
        ->and(Product::count())->toBe(23)
        ->and(Article::count())->toBe(7)
        ->and(Client::count())->toBe(17)
        ->and(GalleryImage::count())->toBe(4)
        ->and(LegacyRedirect::count())->toBe(37);
});

it('stores every product image on the public disk', function () {
    Product::with('images')->get()->each(function (Product $product): void {
        assertMediaExists($product->card_image);
        $product->images->each(fn ($image) => assertMediaExists($image->path));
    });
});

it('stores every brand, article and setting image on the public disk', function () {
    Brand::all()->each(fn (Brand $brand) => assertMediaExists($brand->card_image));

    Article::all()->each(function (Article $article): void {
        assertMediaExists($article->card_image);
        assertMediaExists($article->hero_image);
    });

    SiteSetting::all()->each(function (SiteSetting $settings): void {
        assertMediaExists($settings->logo_path);
        assertMediaExists($settings->favicon_path);
        assertMediaExists($settings->default_social_image);
    });
});

it('stores every client logo and its responsive variants on the public disk', function () {
    Client::all()->each(function (Client $client): void {
        assertMediaExists($client->path);

        foreach ($client->variants ?? [] as $variant) {
            assertMediaExists($variant['path']);
        }
    });
});

it('stores every gallery image, its full size and its variants on the public disk', function () {
    GalleryImage::all()->each(function (GalleryImage $image): void {
        assertMediaExists($image->path);
        assertMediaExists($image->full_path);

        $candidates = [
            ...array_values($image->variants['sources'] ?? []),
            $image->variants['fallback'] ?? [],
        ];

        foreach ($candidates as $list) {
            foreach ($list as $candidate) {
                assertMediaExists($candidate['path']);
            }
        }
    });
});

it('keeps the clients and gallery captions in the same order as the seeded homepage', function () {
    $homeMedia = require database_path('seed-data/home-media.php');

    expect(Client::orderBy('position')->pluck('name')->all())
        ->toBe(array_column($homeMedia['clients'], 'name'))
        ->and(GalleryImage::orderBy('position')->pluck('caption')->all())
        ->toBe(array_column($homeMedia['gallery'], 'caption'));
});

it('imports product specification rows for every product that had one', function () {
    expect(ProductSectionRow::count())->toBeGreaterThan(0);

    Product::with('sections.rows')->get()->each(function (Product $product): void {
        $product->sections->each(function ($section): void {
            expect($section->type)->not->toBeNull()
                ->and($section->rows)->not->toBeEmpty();
        });
    });
});

it('resolves every imported legacy redirect to an existing route', function () {
    LegacyRedirect::all()->each(function (LegacyRedirect $redirect): void {
        expect(Route::has($redirect->route_name))->toBeTrue();

        $this->get($redirect->from_path)->assertRedirect($redirect->destinationUrl());
    });
});
