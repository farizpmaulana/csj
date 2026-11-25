<x-filament-widgets::widget>
    <div class="csj-welcome-banner">
        <div>
            <p class="csj-welcome-eyebrow">
                {{ $companyName }}
            </p>
            <h2 class="csj-welcome-title">
                Welcome back, {{ $userName }}
            </h2>
            <p class="csj-welcome-desc">
                Manage industrial partners, product specifications, homepage showcase, and news articles from one workspace.
            </p>
        </div>

        <div class="csj-welcome-actions">
            <a href="{{ $productsUrl }}" class="csj-dash-btn csj-dash-btn--primary">
                <x-heroicon-o-cube aria-hidden="true" />
                <span>Manage Products</span>
            </a>
            <a href="{{ $articlesUrl }}" class="csj-dash-btn csj-dash-btn--secondary">
                <x-heroicon-o-newspaper aria-hidden="true" />
                <span>Write Article</span>
            </a>
            <a href="{{ $homeUrl }}" target="_blank" rel="noopener noreferrer" class="csj-dash-btn csj-dash-btn--ghost">
                <span>View Live Site</span>
                <x-heroicon-o-arrow-top-right-on-square aria-hidden="true" />
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
