<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Brand;
use App\Models\Client;
use App\Models\GalleryImage;
use App\Models\Product;
use App\Models\ShowcaseProduct;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $siteSettings = SiteSetting::current();

        $brands = Brand::query()
            ->where('is_visible', true)
            ->orderBy('position')
            ->get();

        $clients = Client::query()
            ->where('is_visible', true)
            ->orderBy('position')
            ->get();

        // Curated showcase products from the dedicated showcase table, falling back to legacy curation
        $showcaseItems = ShowcaseProduct::query()
            ->with(['product' => fn ($q) => $q->visible()->with('brand')])
            ->orderBy('position')
            ->get();

        if ($showcaseItems->isNotEmpty()) {
            $partnerProducts = $showcaseItems
                ->map(fn (ShowcaseProduct $item): ?Product => $item->product)
                ->filter(fn (?Product $product): bool => $product !== null && $product->is_visible)
                ->values();
        } else {
            // Curated showcase products, or fall back to one per partner.
            $showcaseProducts = Product::query()
                ->visible()
                ->where('is_showcase', true)
                ->with('brand')
                ->orderBy('showcase_position')
                ->orderBy('name')
                ->get();

            if ($showcaseProducts->isEmpty()) {
                $showcaseProducts = $brands
                    ->map(fn (Brand $brand): ?Product => Product::query()
                        ->visible()
                        ->where('brand_id', $brand->id)
                        ->with('brand')
                        ->ordered()
                        ->first())
                    ->filter()
                    ->values();
            }

            // Interleave so no two consecutive products share a partner.
            $queuesByBrand = $showcaseProducts
                ->groupBy('brand_id')
                ->map(fn ($group) => $group->values());

            $partnerProducts = collect();
            $lastBrandId = null;

            while ($queuesByBrand->contains(fn ($queue) => $queue->isNotEmpty())) {
                // Pick available brands different from the last brand.
                $availableBrands = $queuesByBrand->filter(
                    fn ($queue, $brandId) => $queue->isNotEmpty() && $brandId !== $lastBrandId
                );

                // If only the last brand has products remaining, fallback to it.
                if ($availableBrands->isEmpty()) {
                    $availableBrands = $queuesByBrand->filter(fn ($queue) => $queue->isNotEmpty());
                }

                // Pick the brand with the most remaining products to balance alternation.
                $selectedBrandId = $availableBrands
                    ->sortByDesc(fn ($queue) => $queue->count())
                    ->keys()
                    ->first();

                $nextProduct = $queuesByBrand[$selectedBrandId]->shift();
                $partnerProducts->push($nextProduct);
                $lastBrandId = $nextProduct->brand_id;
            }
        }

        $initialShowcaseProductIds = $partnerProducts
            ->pluck('id')
            ->all();

        return view('home', [
            'siteSettings' => $siteSettings,
            'brands' => $brands,
            'partnerProducts' => $partnerProducts,
            'initialShowcaseProductIds' => $initialShowcaseProductIds,
            'clientCount' => $clients->count(),
            'partnerCount' => $brands->count(),
            'clients' => $clients,
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
