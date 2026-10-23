<article class="catalog-card" data-product-search-card>
    <a
        class="catalog-card-cover-link"
        href="{{ $product->url }}"
        aria-label="View {{ $product->name }} details"
    ></a>
    <div class="catalog-card-image">
        <img
            src="{{ media($product->card_image) }}"
            alt="{{ $product->card_alt }}"
            loading="lazy"
            decoding="async"
        />
    </div>
    <div class="catalog-card-body">
        @if ($product->card_label)
            <p class="catalog-card-spec">{{ $product->card_label }}</p>
        @endif
        <h3 class="catalog-card-title">{{ $product->name }}</h3>
        <p class="catalog-card-spec">{{ $product->excerpt }}</p>
        <div class="catalog-card-actions">
            <a
                class="catalog-card-btn catalog-card-btn-primary"
                href="{{ $product->whatsapp_link }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                Request Price
            </a>
            <a
                class="catalog-card-btn catalog-card-btn-outline"
                href="{{ $product->url }}"
            >
                View Details
            </a>
        </div>
    </div>
</article>
