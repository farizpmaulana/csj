@php
    $title = $productModel->seo_title;
    $description = \Illuminate\Support\Str::limit(trim(strip_tags((string) $productModel->description)), 155);
    $canonical = $productModel->url;
    $ogImage = media($productModel->images->first()?->path);
    $styles = ['css/product-detail.css', 'css/style.css', 'css/responsive.css'];
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
    <main class="product-detail-page">
        <div class="container">
            <a href="{{ route('products.index') }}" class="back-to-partners">
                <span class="back-to-partners-icon" aria-hidden="true">&lsaquo;</span>
                Back to Products
            </a>
            <section class="product-overview" aria-label="{{ $productModel->name }}">
                <div
                    class="product-gallery"
                    data-product-gallery
                    aria-label="{{ $productModel->name }} product image gallery"
                    tabindex="0"
                >
                    <div class="product-gallery-stage">
                        @foreach ($productModel->images as $index => $image)
                            <figure class="product-gallery-slide {{ $index === 0 ? 'is-active' : '' }}">
                                <img
                                    src="{{ media($image->path) }}"
                                    alt="{{ $image->alt }}"
                                    @if ($index === 0)
                                        fetchpriority="high"
                                    @else
                                        loading="lazy"
                                    @endif
                                />
                            </figure>
                        @endforeach

                        <button
                            class="product-gallery-control product-gallery-control--previous"
                            type="button"
                            aria-label="Previous product image"
                        >
                            &lsaquo;
                        </button>
                        <button
                            class="product-gallery-control product-gallery-control--next"
                            type="button"
                            aria-label="Next product image"
                        >
                            &rsaquo;
                        </button>
                        <span class="product-gallery-count" aria-live="polite"></span>
                    </div>
                    <div class="product-gallery-thumbnails" aria-label="Product image thumbnails"></div>
                </div>
                <div class="product-summary">
                    <p class="product-summary-eyebrow">Trusted industrial supply</p>
                    <h1>{{ $productModel->name }}</h1>
                    @if (filled($productModel->subtitle))
                        <p class="product-summary-subtitle">{{ $productModel->subtitle }}</p>
                    @endif
                    <ul class="product-trust-points">
                        <li><span aria-hidden="true">&#10003;</span> Trusted products from an official distributor</li>
                        <li><span aria-hidden="true">&#10003;</span> Responsive consultation and dependable delivery</li>
                    </ul>
                    <div class="product-summary-actions">
                        <a
                            href="{{ $productModel->whatsapp_link }}"
                            class="product-action product-action-primary"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            WhatsApp Consultation
                        </a>
                        <a
                            href="{{ $productModel->whatsapp_link }}"
                            class="product-action product-action-secondary"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            Request Price
                        </a>
                    </div>
                </div>
            </section>

            <a
                class="product-brand-bar"
                href="{{ route('products.index', ['brand' => [$brandModel->slug]]) }}"
                aria-label="View {{ $brandModel->name }} products"
            >
                <img src="{{ media($brandModel->card_image) }}" alt="{{ $brandModel->name }} logo" />
                <span><small>Official partner</small><strong>{{ $brandModel->name }}</strong></span>
                <span class="product-brand-bar-link">View brand products <span aria-hidden="true">&rarr;</span></span>
            </a>

            @if (filled($productModel->description))
                <section class="detail-card">
                    <h2 class="product-section-heading">
                        <span>Product Description</span>
                    </h2>
                    <div class="detail-description">{!! $productModel->description !!}</div>
                </section>
            @endif

            @foreach ($productModel->sections as $section)
                <section class="detail-card">
                    <h2 class="product-section-heading">
                        <span>{{ $section->title }}</span>
                    </h2>

                    @if ($section->type === \App\Enums\ProductSectionType::Table)
                        <table class="detail-table">
                            @foreach ($section->rows as $row)
                                <tr>
                                    <th>{{ $row->label }}</th>
                                    <td>{{ $row->value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @elseif ($section->type === \App\Enums\ProductSectionType::Ordered)
                        <ol class="detail-list">
                            @foreach ($section->rows as $row)
                                <li>{{ $row->label }}</li>
                            @endforeach
                        </ol>
                    @else
                        <ul class="detail-list">
                            @foreach ($section->rows as $row)
                                <li>{{ $row->label }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach

            <section class="product-cta-banner">
                <div class="cta-copy">
                    <span class="product-cta-eyebrow">Product support</span>
                    <h3>Need a quote or more product information?</h3>
                    <p>Our team can help with availability, delivery, and the right fit for your project.</p>
                </div>
                <div class="cta-actions">
                    <a href="{{ $productModel->whatsapp_link }}" class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer">
                        WhatsApp Consultation <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-gallery.js') }}"></script>
@endpush
