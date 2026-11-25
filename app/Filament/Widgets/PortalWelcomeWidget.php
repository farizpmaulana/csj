<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Widgets\Widget;

class PortalWelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.portal-welcome-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $settings = SiteSetting::current();

        return [
            'companyName' => $settings->company_name ?: 'PT Cahaya Sarange Jaya',
            'userName' => auth()->user()?->name ?? 'Administrator',
            'homeUrl' => route('home'),
            'productsUrl' => ProductResource::getUrl('index'),
            'articlesUrl' => ArticleResource::getUrl('index'),
            'settingsUrl' => SiteSettingResource::getUrl('index'),
        ];
    }
}
