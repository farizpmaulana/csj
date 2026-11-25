<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\GalleryImages\GalleryImageResource;
use App\Filament\Resources\LegacyRedirects\LegacyRedirectResource;
use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Widgets\Widget;

class SiteIdentityWidget extends Widget
{
    protected string $view = 'filament.widgets.site-identity-widget';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $settings = SiteSetting::current();

        return [
            'settings' => $settings,
            'settingsUrl' => SiteSettingResource::getUrl('index'),
            'clientsUrl' => ClientResource::getUrl('index'),
            'galleryUrl' => GalleryImageResource::getUrl('index'),
            'categoriesUrl' => ProductCategoryResource::getUrl('index'),
            'redirectsUrl' => LegacyRedirectResource::getUrl('index'),
        ];
    }
}
