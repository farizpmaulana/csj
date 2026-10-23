<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyPageController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
| Slugs are lowercase and dash separated. The constraint keeps legacy ".html"
| addresses from being swallowed by these routes so the catch-all below can
| redirect them.
*/
$slug = '[a-z0-9]+(?:-[a-z0-9]+)*';

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/products/{brand}', fn (string $brand) => redirect()->route('products.index', ['brand' => [$brand]]))
    ->where('brand', $slug);

Route::get('/products/{brand}/{product}', [ProductController::class, 'show'])
    ->where(['brand' => $slug, 'product' => $slug])
    ->name('products.show');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

Route::get('/articles/{article}', [ArticleController::class, 'show'])
    ->where('article', $slug)
    ->name('articles.show');

/*
| Legacy .html addresses from the previous static site keep working through a
| permanent redirect. This must stay last so the real routes win.
*/
Route::get('/{path}', LegacyPageController::class)
    ->where('path', 'products/.*|articles/.*|index\.html')
    ->name('legacy');
