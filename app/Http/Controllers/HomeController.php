<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $brands = Brand::query()
            ->where('is_visible', true)
            ->orderBy('position')
            ->get();

        $partnerProducts = Product::query()
            ->visible()
            ->with('brand')
            ->ordered()
            ->get();

        $productsByBrand = $partnerProducts
            ->groupBy('brand_id')
            ->map(fn ($products) => $products->shuffle())
            ->values();

        $maxProductsPerBrand = $productsByBrand->max(fn ($products): int => $products->count()) ?? 0;
        $partnerProducts = collect();

        for ($productIndex = 0; $productIndex < $maxProductsPerBrand; $productIndex++) {
            foreach ($productsByBrand as $brandProducts) {
                $product = $brandProducts->get($productIndex);

                if ($product !== null) {
                    $partnerProducts->push($product);
                }
            }
        }

        $initialShowcaseProductIds = $partnerProducts
            ->take(8)
            ->pluck('id')
            ->all();

        return view('home', [
            'brands' => $brands,
            'partnerProducts' => $partnerProducts,
            'initialShowcaseProductIds' => $initialShowcaseProductIds,
            'clients' => Client::query()
                ->where('is_visible', true)
                ->orderBy('position')
                ->get(),
            'gallery' => GalleryImage::query()
                ->where('is_visible', true)
                ->orderBy('position')
                ->get(),
            'latestArticles' => Article::query()
                ->published()
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
