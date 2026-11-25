<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\ShowcaseProducts\ShowcaseProductResource;
use App\Models\Brand;
use App\Models\ShowcaseProduct;
use Filament\Widgets\Widget;

class PartnerCatalogWidget extends Widget
{
    protected string $view = 'filament.widgets.partner-catalog-widget';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $brands = Brand::query()
            ->withCount('products')
            ->orderBy('position')
            ->get();

        $showcaseItems = ShowcaseProduct::query()
            ->with(['product.brand'])
            ->orderBy('position')
            ->limit(6)
            ->get();

        return [
            'brands' => $brands,
            'showcaseItems' => $showcaseItems,
            'brandsUrl' => BrandResource::getUrl('index'),
            'showcaseUrl' => ShowcaseProductResource::getUrl('index'),
        ];
    }
}
