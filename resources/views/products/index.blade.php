@php
    $title = 'All Products - PT Cahaya Sarange Jaya';
    $description = 'Browse products across our brands and supply categories.';
    $canonical = route('products.index');
    $styles = ['css/product-detail.css', 'css/style.css', 'css/responsive.css'];
    $fontAwesome = true;
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
    <header class="product-hero products-index-hero">
        <div class="container">
            <a class="page-back-link page-back-link-light" href="{{ route('home') }}">
                <span aria-hidden="true">&larr;</span> Back to Home
            </a>
            <h1>All Products</h1>
            <p>Browse our product range by partner, then open a product for details and a quotation.</p>
        </div>
    </header>

    <main class="product-content products-index-content">
        <div class="container">
            <div class="products-catalog-layout">
                <aside class="products-filter-sidebar" aria-label="Product filters">
                    <form method="get" action="{{ route('products.index') }}" data-products-brand-filter-form>
                        @if ($searchTerm !== '')
                            <input type="hidden" name="q" value="{{ $searchTerm }}" />
                        @endif
                        <h2 class="products-filter-title">
                            <i class="fas fa-sliders" aria-hidden="true"></i>
                            Filter products
                        </h2>

                        <div class="products-filter-options">
                            <label class="products-filter-option products-filter-option-all">
                                <input
                                    type="checkbox"
                                    value="all"
                                    data-products-filter-all
                                    @checked($selectedBrandSlugs === [])
                                />
                                <span class="products-filter-checkbox" aria-hidden="true"></span>
                                <span>All Products</span>
                            </label>

                            @foreach ($filterBrands as $filterBrand)
                                <label class="products-filter-option">
                                    <input
                                        type="checkbox"
                                        name="brand[]"
                                        value="{{ $filterBrand->slug }}"
                                        @checked(in_array($filterBrand->slug, $selectedBrandSlugs, true))
                                    />
                                    <span class="products-filter-checkbox" aria-hidden="true"></span>
                                    <span>{{ $filterBrand->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <noscript>
                            <button class="products-filter-submit" type="submit">Apply filters</button>
                        </noscript>
                    </form>
                </aside>

                <div class="products-catalog-results">
                    <form class="products-search-form" method="get" action="{{ route('products.index') }}" role="search" data-products-search-form>
                        @foreach ($selectedBrandSlugs as $selectedBrandSlug)
                            <input type="hidden" name="brand[]" value="{{ $selectedBrandSlug }}" />
                        @endforeach
                        <label class="visually-hidden" for="products-search">Search products</label>
                        <div class="products-search-field">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <input
                                id="products-search"
                                type="search"
                                name="q"
                                aria-label="Search products"
                                value="{{ $searchTerm }}"
                                placeholder="Search products, models, or specifications"
                                autocomplete="off"
                            />
                            <a
                                class="products-search-clear"
                                href="{{ route('products.index', ['brand' => $selectedBrandSlugs]) }}"
                                data-products-search-clear
                                @if ($searchTerm === '') hidden @endif
                            >
                                Clear
                            </a>
                        </div>
                        <button type="submit" class="products-search-submit">Search</button>
                    </form>
                    <p class="catalog-empty products-search-empty" data-products-search-empty role="status" hidden></p>
                    @forelse ($brands as $brandModel)
                        <section
                            class="products-brand-section"
                            aria-labelledby="products-brand-{{ $brandModel->slug }}"
                        >
                            <header class="products-brand-heading">
                                <div class="products-brand-heading-main">
                                    <img
                                        class="products-brand-heading-logo"
                                        src="{{ media($brandModel->card_image) }}"
                                        alt="{{ $brandModel->name }} logo"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <div>
                                        <p class="products-brand-heading-kicker">Official partner</p>
                                        <h2 id="products-brand-{{ $brandModel->slug }}">{{ $brandModel->name }}</h2>
                                        @if ($brandModel->headline)
                                            <p>{{ $brandModel->headline }}</p>
                                        @endif
                                    </div>
                                </div>
                                <a
                                    class="products-brand-details"
                                    href="{{ route('products.index', ['brand' => [$brandModel->slug]]) }}"
                                >
                                    View products <span aria-hidden="true">&rarr;</span>
                                </a>
                            </header>

                            @if ($brandModel->products->isNotEmpty())
                                <div class="catalog-grid">
                                    @foreach ($brandModel->products as $product)
                                        @include('products.partials.card', ['product' => $product])
                                    @endforeach
                                </div>
                            @else
                                <p class="catalog-empty">Products for this brand will be listed here soon.</p>
                            @endif
                        </section>
                    @empty
                        <p class="catalog-empty">
                            @if ($searchTerm !== '')
                                No products matched &ldquo;{{ $searchTerm }}&rdquo;. Try another search.
                            @else
                                No visible products are available for this selection.
                            @endif
                        </p>
                    @endforelse
                </div>
            </div>

            <section class="product-cta-banner products-index-cta">
                <div class="cta-copy">
                    <span class="product-cta-eyebrow">Product support</span>
                    <h3>Need help finding the right products?</h3>
                    <p>Talk to our team for product recommendations, availability, and pricing for your operation.</p>
                </div>
                <div class="cta-actions">
                    <a href="{{ wa_link() }}" class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer">
                        WhatsApp Consultation <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <script>
        const productsSearchForm = document.querySelector('[data-products-search-form]');
        const productsSearchInput = productsSearchForm?.querySelector('input[name="q"]');
        const productsSearchClear = document.querySelector('[data-products-search-clear]');
        const productsSearchEmpty = document.querySelector('[data-products-search-empty]');
        const productsBrandFilterForm = document.querySelector('[data-products-brand-filter-form]');
        const productsBrandSections = [...document.querySelectorAll('.products-brand-section')];

        const updateProductSearch = () => {
            const rawSearchTerm = productsSearchInput?.value.trim() ?? '';
            const searchTerm = rawSearchTerm.toLocaleLowerCase();
            let visibleProductCount = 0;

            productsBrandSections.forEach((section) => {
                const brandName = section.querySelector('.products-brand-heading h2')?.textContent ?? '';
                const productCards = [...section.querySelectorAll('[data-product-search-card]')];
                let visibleBrandProductCount = 0;

                productCards.forEach((card) => {
                    const searchableText = [
                        brandName,
                        card.querySelector('.catalog-card-title')?.textContent ?? '',
                        ...[...card.querySelectorAll('.catalog-card-spec')].map((element) => element.textContent ?? ''),
                    ].join(' ').toLocaleLowerCase();
                    const matchesSearch = searchTerm === '' || searchableText.includes(searchTerm);

                    card.hidden = !matchesSearch;
                    visibleBrandProductCount += matchesSearch ? 1 : 0;
                });

                section.hidden = visibleBrandProductCount === 0;
                visibleProductCount += visibleBrandProductCount;
            });

            if (productsSearchClear) {
                productsSearchClear.hidden = rawSearchTerm === '';
            }

            let productsFilterSearchInput = productsBrandFilterForm?.querySelector('input[name="q"]');
            if (rawSearchTerm !== '') {
                if (!productsFilterSearchInput && productsBrandFilterForm) {
                    productsFilterSearchInput = document.createElement('input');
                    productsFilterSearchInput.type = 'hidden';
                    productsFilterSearchInput.name = 'q';
                    productsBrandFilterForm.append(productsFilterSearchInput);
                }
                if (productsFilterSearchInput) {
                    productsFilterSearchInput.value = rawSearchTerm;
                }
            } else {
                productsFilterSearchInput?.remove();
            }

            if (productsSearchEmpty) {
                productsSearchEmpty.hidden = visibleProductCount > 0;
                productsSearchEmpty.textContent = searchTerm
                    ? `No products matched "${rawSearchTerm}". Try another search.`
                    : 'No products are available for this selection.';
            }

            const currentUrl = new URL(window.location.href);
            if (rawSearchTerm !== '') {
                currentUrl.searchParams.set('q', rawSearchTerm);
            } else {
                currentUrl.searchParams.delete('q');
            }
            window.history.replaceState({}, '', currentUrl);
        };

        productsSearchForm?.addEventListener('submit', (event) => {
            event.preventDefault();
            updateProductSearch();
        });
        productsSearchInput?.addEventListener('input', updateProductSearch);
        updateProductSearch();

        document.querySelectorAll('[data-products-brand-filter-form]').forEach((form) => {
            const allProductsCheckbox = form.querySelector('[data-products-filter-all]');
            const brandCheckboxes = [...form.querySelectorAll('input[name="brand[]"]')];

            form.addEventListener('change', (event) => {
                if (event.target === allProductsCheckbox && allProductsCheckbox.checked) {
                    brandCheckboxes.forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                } else if (event.target.checked) {
                    allProductsCheckbox.checked = false;
                }

                form.requestSubmit();
            });
        });
    </script>
@endsection

@section('footer')
    @include('partials.footer')
@endsection
