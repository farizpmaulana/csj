<x-filament-widgets::widget>
    <x-filament::section>
        <div>
            <div class="csj-panel-header">
                <div>
                    <h3 class="csj-panel-title">Partner Catalog Overview</h3>
                    <p class="csj-panel-subtitle">
                        Live product distribution across your official industrial partners.
                    </p>
                </div>
                <a href="{{ $brandsUrl }}" class="csj-panel-link">
                    Manage Partners
                </a>
            </div>

            @if ($brands->isNotEmpty())
                <div class="csj-partner-grid">
                    @foreach ($brands as $brand)
                        <div class="csj-partner-card">
                            <div class="csj-partner-left">
                                @if (filled($brand->card_image))
                                    <img
                                        src="{{ media($brand->card_image) }}"
                                        alt="{{ $brand->name }}"
                                        class="csj-partner-logo"
                                    />
                                @else
                                    <div class="csj-partner-fallback">
                                        {{ strtoupper(substr($brand->name, 0, 3)) }}
                                    </div>
                                @endif
                                <div style="min-width: 0;">
                                    <p class="csj-partner-name">{{ $brand->name }}</p>
                                    <p class="csj-partner-count">
                                        {{ $brand->products_count }} {{ \Illuminate\Support\Str::plural('product', $brand->products_count) }}
                                    </p>
                                </div>
                            </div>
                            <span class="csj-status-pill {{ $brand->is_visible ? 'csj-status-pill--live' : 'csj-status-pill--hidden' }}">
                                {{ $brand->is_visible ? 'Live' : 'Hidden' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="csj-panel-subtitle" style="padding: 1.25rem 0; text-align: center;">
                    No partners configured yet.
                </p>
            @endif

            <div class="csj-showcase-wrap">
                <div class="csj-showcase-header">
                    <h4 class="csj-showcase-title">Homepage Showcase Lineup</h4>
                    <a href="{{ $showcaseUrl }}" class="csj-panel-link">
                        Reorder Showcase
                    </a>
                </div>

                @if ($showcaseItems->isNotEmpty())
                    <div class="csj-showcase-grid">
                        @foreach ($showcaseItems as $index => $item)
                            @if ($item->product)
                                <div class="csj-showcase-item">
                                    <span class="csj-showcase-index">{{ $index + 1 }}</span>
                                    <div style="min-width: 0;">
                                        <p class="csj-partner-name" style="font-size: 0.75rem;">{{ $item->product->name }}</p>
                                        <p class="csj-partner-count">{{ $item->product->brand?->name }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="csj-panel-subtitle">
                        No products currently pinned to the homepage showcase.
                    </p>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
