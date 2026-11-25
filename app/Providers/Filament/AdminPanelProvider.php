<?php

namespace App\Providers\Filament;

use App\Models\SiteSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $primary = Color::Blue;
        $primary[500] = '#2563eb'; // 5.17:1 contrast with white text (WCAG AA)
        $primary[600] = '#1d4ed8'; // 6.70:1 contrast with white text (WCAG AA)
        $primary[700] = '#1e40af';

        $gray = Color::Zinc;
        $gray[950] = 'oklch(0.215 0.006 285.885)';
        $gray[900] = 'oklch(0.280 0.006 286.033)';
        $gray[800] = 'oklch(0.350 0.008 286.033)';
        $gray[700] = 'oklch(0.440 0.010 286.033)';

        return $panel
            ->default()
            ->id('admin')
            ->path('admin-cms')
            ->brandName('CSJ Content Portal')
            ->brandLogo(fn () => view('filament.admin.brand'))
            ->favicon(fn (): ?string => filled($path = SiteSetting::current()->favicon_path) ? media($path) : asset('images/favicon.svg'))
            ->maxContentWidth(Width::Full)
            ->login()
            ->colors([
                'primary' => $primary,
                'gray' => $gray,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                <style>
                    .fi-fo-file-upload .filepond--root {
                        max-width: 420px;
                    }
                    .fi-fo-file-upload .filepond--image-preview {
                        background-color: #f8fafc;
                    }
                    .dark .fi-fo-file-upload .filepond--image-preview {
                        background-color: #1f2430;
                    }
                    .fi-fo-file-upload .filepond--image-preview-wrapper img,
                    .fi-fo-file-upload .filepond--image-preview canvas {
                        object-fit: contain !important;
                    }
                    .fi-modal-window.fi-width-5xl {
                        display: flex;
                        flex-direction: column;
                        height: min(84vh, 760px);
                        max-height: 84vh;
                    }
                    .fi-modal-window.fi-width-5xl .fi-modal-content {
                        flex: 1 1 auto;
                        overflow-y: auto;
                    }
                </style>
                HTML),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
