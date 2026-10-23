<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $searchTerm = $request->query('q', '');

        abort_if(! is_string($searchTerm), 404);

        $searchTerm = mb_substr(trim($searchTerm), 0, 120);
        $requestedBrands = $request->query('brand', []);

        if (is_string($requestedBrands)) {
            $requestedBrands = [$requestedBrands];
        }

        abort_if(! is_array($requestedBrands), 404);

        foreach ($requestedBrands as $requestedBrand) {
            abort_if(! is_string($requestedBrand), 404);
        }

        $selectedBrandSlugs = array_values(array_unique(array_filter(
            $requestedBrands,
            fn (string $brandSlug): bool => $brandSlug !== '',
        )));

        $filterBrands = Brand::query()
            ->where('is_visible', true)
            ->orderBy('position')
            ->get(['id', 'slug', 'name']);

        abort_if(
            array_diff($selectedBrandSlugs, $filterBrands->pluck('slug')->all()) !== [],
            404,
        );

        $brandsQuery = Brand::query()
            ->where('is_visible', true)
            ->orderBy('position')
            ->with(['products' => function (HasMany $query): void {
                $query->visible()->ordered()->with('brand');
            }]);

        if ($selectedBrandSlugs !== []) {
            $brandsQuery->whereIn('slug', $selectedBrandSlugs);
        }

        $brands = $brandsQuery->get();

        abort_if($selectedBrandSlugs !== [] && $brands->isEmpty(), 404);

        return view('products.index', [
            'brands' => $brands,
            'filterBrands' => $filterBrands,
            'searchTerm' => $searchTerm,
            'selectedBrandSlugs' => $selectedBrandSlugs,
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
