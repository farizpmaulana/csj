<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function brand(string $brand): View
    {
        $brands = config('catalog.brands');

        abort_unless(isset($brands[$brand]), 404);

        return view('products.brand', [
            'brandKey' => $brand,
            'brand' => $brands[$brand],
            'products' => config('catalog.products'),
        ]);
    }

    public function show(string $brand, string $product): View
    {
        $brands = config('catalog.brands');
        $products = config('catalog.products');

        abort_unless(isset($brands[$brand]), 404);
        abort_unless(isset($products[$product]), 404);
        abort_unless($products[$product]['brand'] === $brand, 404);

        return view('products.show', [
            'brandKey' => $brand,
            'brand' => $brands[$brand],
            'productSlug' => $product,
            'product' => $products[$product],
        ]);
    }
}
