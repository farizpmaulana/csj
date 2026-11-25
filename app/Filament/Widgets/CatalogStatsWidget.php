<?php

namespace App\Filament\Widgets;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 3,
        ];
    }

    protected function getStats(): array
    {
        $visibleBrands = Brand::query()->where('is_visible', true)->count();
        $totalBrands = Brand::query()->count();

        $visibleClients = Client::query()->where('is_visible', true)->count();
        $totalClients = Client::query()->count();

        $visibleProducts = Product::query()->visible()->count();
        $totalProducts = Product::query()->count();

        return [
            Stat::make('Active Partners', "{$visibleBrands} / {$totalBrands}")
                ->description('Visible brand partners on website')
                ->descriptionIcon(Heroicon::OutlinedBuildingStorefront)
                ->color('primary'),
            Stat::make('Clients', "{$visibleClients} / {$totalClients}")
                ->description('Visible client logos on homepage')
                ->descriptionIcon(Heroicon::OutlinedBuildingOffice2)
                ->color('info'),
            Stat::make('Published Products', "{$visibleProducts} / {$totalProducts}")
                ->description('Live catalog items')
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('success'),
        ];
    }
}
