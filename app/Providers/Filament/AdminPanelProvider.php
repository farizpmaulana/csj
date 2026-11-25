<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\CatalogStatsWidget;
use App\Filament\Widgets\PartnerCatalogWidget;
use App\Filament\Widgets\PortalWelcomeWidget;
use App\Filament\Widgets\SiteIdentityWidget;
use App\Models\SiteSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
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
            ->font('Plus Jakarta Sans')
            ->maxContentWidth(Width::Full)
            ->login()
            ->colors([
                'primary' => $primary,
                'gray' => $gray,
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): HtmlString => new HtmlString('<p class="mb-2 text-center text-xs text-gray-500 dark:text-gray-400">Website Catalog, Articles &amp; Media Management</p>'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                <style>
                    /* CSJ Content Portal visual polish */
                    .fi-body {
                        background-color: #f4f7fa;
                    }
                    .dark .fi-body {
                        background-color: #191c22;
                    }
                    .fi-sidebar {
                        border-right: 1px solid #e2e8f0;
                    }
                    .dark .fi-sidebar {
                        border-right: 1px solid #2b313c;
                    }
                    .fi-sidebar-item-active > .fi-sidebar-item-btn {
                        border-left: 3px solid #2563eb;
                        background-color: rgba(37, 99, 235, 0.08);
                    }
                    .dark .fi-sidebar-item-active > .fi-sidebar-item-btn {
                        border-left-color: #60a5fa;
                        background-color: rgba(96, 165, 250, 0.12);
                    }
                    .fi-simple-main {
                        border-top: 4px solid #022e54;
                        box-shadow: 0 12px 32px rgba(2, 46, 84, 0.08);
                    }
                    .dark .fi-simple-main {
                        border-top-color: #2563eb;
                        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
                    }
                    .fi-ta-header-cell-label {
                        font-size: 0.72rem;
                        letter-spacing: 0.04em;
                        text-transform: uppercase;
                    }
                    .fi-ta-row:hover {
                        background-color: rgba(2, 46, 84, 0.025);
                    }
                    .dark .fi-ta-row:hover {
                        background-color: rgba(255, 255, 255, 0.03);
                    }
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

                    /* Collapsible Product Specifications header with dropdown chevron beside label */
                    .csj-spec-repeater .fi-fo-repeater-item-header {
                        cursor: pointer;
                    }
                    .csj-spec-repeater .fi-fo-repeater-item-header-label {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                    }
                    .csj-spec-repeater .fi-fo-repeater-item-header-label::before {
                        content: '';
                        width: 0.55rem;
                        height: 0.55rem;
                        flex-shrink: 0;
                        border-right: 2px solid currentColor;
                        border-bottom: 2px solid currentColor;
                        opacity: 0.65;
                        transform: translateY(-1px) rotate(45deg);
                        transition: transform 160ms ease;
                    }
                    .csj-spec-repeater .fi-fo-repeater-item.fi-collapsed .fi-fo-repeater-item-header-label::before {
                        transform: translateY(0) rotate(-45deg);
                    }
                    .csj-spec-repeater .fi-fo-repeater-item-header-collapsible-actions {
                        display: none;
                    }

                    /* Custom Dashboard Widgets (explicit CSS so layout & icon sizes never depend on uncompiled Tailwind classes) */
                    .csj-welcome-banner {
                        display: flex;
                        flex-direction: column;
                        gap: 1.25rem;
                        padding: 1.5rem;
                        border-radius: 0.75rem;
                        border: 1px solid #0b3e6b;
                        background: #022e54;
                        color: #ffffff;
                        box-shadow: 0 2px 8px rgba(2, 46, 84, 0.14);
                    }
                    @media (min-width: 1024px) {
                        .csj-welcome-banner {
                            flex-direction: row;
                            align-items: center;
                            justify-content: space-between;
                        }
                    }
                    .csj-welcome-eyebrow {
                        margin: 0 0 0.25rem;
                        font-size: 0.75rem;
                        font-weight: 700;
                        letter-spacing: 0.08em;
                        text-transform: uppercase;
                        color: #93c5fd;
                    }
                    .csj-welcome-title {
                        margin: 0 0 0.35rem;
                        font-size: 1.375rem;
                        font-weight: 700;
                        line-height: 1.25;
                        color: #ffffff;
                    }
                    .csj-welcome-desc {
                        margin: 0;
                        max-width: 40rem;
                        font-size: 0.875rem;
                        line-height: 1.5;
                        color: #e2e8f0;
                    }
                    .csj-welcome-actions {
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;
                        gap: 0.625rem;
                    }
                    .csj-dash-btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 0.5rem 0.875rem;
                        border-radius: 0.5rem;
                        font-size: 0.875rem;
                        font-weight: 600;
                        line-height: 1.25;
                        text-decoration: none;
                        cursor: pointer;
                        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
                    }
                    .csj-dash-btn svg {
                        width: 1rem;
                        height: 1rem;
                        flex-shrink: 0;
                    }
                    .csj-dash-btn--primary {
                        background: #2563eb;
                        color: #ffffff;
                        border: 1px solid #2563eb;
                    }
                    .csj-dash-btn--primary:hover {
                        background: #1d4ed8;
                        border-color: #1d4ed8;
                    }
                    .csj-dash-btn--secondary {
                        background: rgba(255, 255, 255, 0.12);
                        color: #ffffff;
                        border: 1px solid rgba(255, 255, 255, 0.22);
                    }
                    .csj-dash-btn--secondary:hover {
                        background: rgba(255, 255, 255, 0.22);
                    }
                    .csj-dash-btn--ghost {
                        background: transparent;
                        color: #bae6fd;
                        border: 1px solid rgba(255, 255, 255, 0.22);
                    }
                    .csj-dash-btn--ghost:hover {
                        color: #ffffff;
                        border-color: rgba(255, 255, 255, 0.45);
                    }

                    .csj-panel-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 1rem;
                        padding-bottom: 1rem;
                        margin-bottom: 1rem;
                        border-bottom: 1px solid #e2e8f0;
                    }
                    .dark .csj-panel-header {
                        border-bottom-color: #303642;
                    }
                    .csj-panel-title {
                        margin: 0;
                        font-size: 1rem;
                        font-weight: 700;
                        color: #0f172a;
                    }
                    .dark .csj-panel-title {
                        color: #f8fafc;
                    }
                    .csj-panel-subtitle {
                        margin: 0.15rem 0 0;
                        font-size: 0.75rem;
                        color: #64748b;
                    }
                    .dark .csj-panel-subtitle {
                        color: #94a3b8;
                    }
                    .csj-panel-link {
                        font-size: 0.75rem;
                        font-weight: 600;
                        color: #2563eb;
                        text-decoration: none;
                        white-space: nowrap;
                    }
                    .csj-panel-link:hover {
                        text-decoration: underline;
                    }
                    .dark .csj-panel-link {
                        color: #60a5fa;
                    }

                    .csj-partner-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 0.75rem;
                    }
                    @media (min-width: 640px) {
                        .csj-partner-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                        }
                    }
                    .csj-partner-card {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 0.75rem;
                        padding: 0.75rem;
                        border-radius: 0.5rem;
                        border: 1px solid #e2e8f0;
                        background: #f8fafc;
                    }
                    .dark .csj-partner-card {
                        border-color: #303642;
                        background: rgba(15, 23, 42, 0.35);
                    }
                    .csj-partner-left {
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        min-width: 0;
                    }
                    .csj-partner-logo {
                        width: 3.5rem;
                        height: 2.5rem;
                        flex-shrink: 0;
                        border-radius: 0.375rem;
                        border: 1px solid #e2e8f0;
                        background: #ffffff;
                        object-fit: contain;
                        padding: 0.25rem;
                    }
                    .dark .csj-partner-logo {
                        border-color: #3b4252;
                    }
                    .csj-partner-fallback {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 3.5rem;
                        height: 2.5rem;
                        flex-shrink: 0;
                        border-radius: 0.375rem;
                        border: 1px solid #e2e8f0;
                        background: #ffffff;
                        font-size: 0.75rem;
                        font-weight: 700;
                        color: #64748b;
                    }
                    .csj-partner-name {
                        margin: 0;
                        font-size: 0.875rem;
                        font-weight: 600;
                        color: #0f172a;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }
                    .dark .csj-partner-name {
                        color: #f8fafc;
                    }
                    .csj-partner-count {
                        margin: 0;
                        font-size: 0.75rem;
                        color: #64748b;
                    }
                    .dark .csj-partner-count {
                        color: #94a3b8;
                    }
                    .csj-status-pill {
                        flex-shrink: 0;
                        padding: 0.15rem 0.5rem;
                        border-radius: 0.375rem;
                        font-size: 0.75rem;
                        font-weight: 600;
                    }
                    .csj-status-pill--live {
                        background: #ecfdf5;
                        color: #047857;
                    }
                    .dark .csj-status-pill--live {
                        background: rgba(16, 185, 129, 0.16);
                        color: #6ee7b7;
                    }
                    .csj-status-pill--hidden {
                        background: #f1f5f9;
                        color: #475569;
                    }
                    .dark .csj-status-pill--hidden {
                        background: #334155;
                        color: #cbd5e1;
                    }

                    .csj-showcase-wrap {
                        margin-top: 1.25rem;
                        padding-top: 1rem;
                        border-top: 1px solid #e2e8f0;
                    }
                    .dark .csj-showcase-wrap {
                        border-top-color: #303642;
                    }
                    .csj-showcase-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 1rem;
                        margin-bottom: 0.75rem;
                    }
                    .csj-showcase-title {
                        margin: 0;
                        font-size: 0.875rem;
                        font-weight: 600;
                        color: #0f172a;
                    }
                    .dark .csj-showcase-title {
                        color: #f8fafc;
                    }
                    .csj-showcase-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 0.5rem;
                    }
                    @media (min-width: 640px) {
                        .csj-showcase-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                        }
                    }
                    @media (min-width: 1024px) {
                        .csj-showcase-grid {
                            grid-template-columns: repeat(3, minmax(0, 1fr));
                        }
                    }
                    .csj-showcase-item {
                        display: flex;
                        align-items: center;
                        gap: 0.625rem;
                        padding: 0.5rem 0.75rem;
                        border-radius: 0.5rem;
                        border: 1px solid #e2e8f0;
                        font-size: 0.75rem;
                    }
                    .dark .csj-showcase-item {
                        border-color: #303642;
                    }
                    .csj-showcase-index {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 1.25rem;
                        height: 1.25rem;
                        flex-shrink: 0;
                        border-radius: 0.25rem;
                        background: #f1f5f9;
                        font-weight: 700;
                        color: #334155;
                    }
                    .dark .csj-showcase-index {
                        background: #303642;
                        color: #e2e8f0;
                    }

                    .csj-dl {
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        gap: 0.625rem;
                        font-size: 0.75rem;
                    }
                    .csj-dl-row {
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 0.75rem;
                    }
                    .csj-dl-row dt {
                        color: #64748b;
                        font-weight: 500;
                    }
                    .dark .csj-dl-row dt {
                        color: #94a3b8;
                    }
                    .csj-dl-row dd {
                        margin: 0;
                        text-align: right;
                        font-weight: 600;
                        color: #0f172a;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .dark .csj-dl-row dd {
                        color: #f8fafc;
                    }
                    .csj-quick-links {
                        margin-top: 1.25rem;
                        padding-top: 1rem;
                        border-top: 1px solid #e2e8f0;
                    }
                    .dark .csj-quick-links {
                        border-top-color: #303642;
                    }
                    .csj-quick-heading {
                        margin: 0 0 0.625rem;
                        font-size: 0.7rem;
                        font-weight: 700;
                        letter-spacing: 0.05em;
                        text-transform: uppercase;
                        color: #64748b;
                    }
                    .dark .csj-quick-heading {
                        color: #94a3b8;
                    }
                    .csj-quick-list {
                        display: flex;
                        flex-direction: column;
                        gap: 0.375rem;
                    }
                    .csj-quick-item {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 0.5rem 0.75rem;
                        border-radius: 0.5rem;
                        border: 1px solid #e2e8f0;
                        font-size: 0.75rem;
                        font-weight: 500;
                        color: #334155;
                        text-decoration: none;
                        transition: border-color 150ms ease, color 150ms ease;
                    }
                    .csj-quick-item svg {
                        width: 0.875rem;
                        height: 0.875rem;
                        flex-shrink: 0;
                    }
                    .csj-quick-item:hover {
                        border-color: #2563eb;
                        color: #2563eb;
                    }
                    .dark .csj-quick-item {
                        border-color: #303642;
                        color: #e2e8f0;
                    }
                    .dark .csj-quick-item:hover {
                        border-color: #60a5fa;
                        color: #60a5fa;
                    }
                    @media (prefers-reduced-motion: reduce) {
                        *, *::before, *::after {
                            transition-duration: 0.01ms !important;
                            animation-duration: 0.01ms !important;
                        }
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
                PortalWelcomeWidget::class,
                CatalogStatsWidget::class,
                PartnerCatalogWidget::class,
                SiteIdentityWidget::class,
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
