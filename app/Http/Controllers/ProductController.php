<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function brand(string $brand): View
    {
        $brandModel = Brand::query()
            ->where('slug', $brand)
            ->where('is_visible', true)
            ->with(['categories' => fn ($query) => $query->orderBy('position'),
                'categories.products' => fn ($query) => $query->visible()->orderBy('position'),
                'categories.products.brand',
                'uncategorisedProducts' => fn ($query) => $query->visible()->with('brand'),
            ])
            ->firstOrFail();

        return view('products.brand', [
            'brandModel' => $brandModel,
        ]);
    }

    public function show(string $brand, string $product): View
    {
        $brandModel = Brand::query()
            ->where('slug', $brand)
            ->where('is_visible', true)
            ->firstOrFail();

        $productModel = Product::query()
            ->whereBelongsTo($brandModel)
            ->where('slug', $product)
            ->where('is_visible', true)
            ->firstOrFail();

        return view('products.show', [
            'brandModel' => $brandModel,
            'productModel' => $productModel,
        ]);
    }
}
