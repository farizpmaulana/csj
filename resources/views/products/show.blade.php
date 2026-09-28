@php
    $title = $product['seo_title'];
    $description = $product['description'];
    $canonical = route('products.show', ['brand' => $brandKey, 'product' => $productSlug]);
    $ogImage = asset($product['gallery'][0]['src']);
    $styles = ['css/product-detail.css', 'css/style.css'];
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar', [
        'variant' => 'inner',
        'innerClass' => 'products-navbar',
        'backUrl' => route('products.brand', ['brand' => $brandKey]),
        'backLabel' => 'Back to Products',
    ])
@endsection

@section('content')
    <header class="product-hero">
        <div class="container">
            <h1>{{ $product['name'] }}</h1>
            <p>{{ $product['subtitle'] }}</p>
        </div>
    </header>

    <section
        class="product-gallery"
        data-product-gallery
        aria-label="{{ $product['name'] }} product image gallery"
        tabindex="0"
    >
        <div class="product-gallery-inner">
            <div class="product-gallery-stage">
                @foreach ($product['gallery'] as $index => $image)
                    <figure class="product-gallery-slide {{ $index === 0 ? 'is-active' : '' }}">
                        <img
                            src="{{ asset($image['src']) }}"
                            alt="{{ $image['alt'] }}"
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
    </section>

    <main class="product-content">
        <div class="container">
            <section class="detail-card">
                <h2>Product Description</h2>
                <p class="detail-description">{{ $product['description'] }}</p>
            </section>

            @foreach ($product['sections'] as $section)
                <section class="detail-card">
                    <h2>{{ $section['title'] }}</h2>

                    @if ($section['type'] === 'table')
                        <table class="detail-table">
                            @foreach ($section['rows'] as $row)
                                <tr>
                                    <th>{{ $row['label'] }}</th>
                                    <td>{{ $row['value'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @elseif ($section['type'] === 'ordered')
                        <ol class="detail-list">
                            @foreach ($section['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ol>
                    @else
                        <ul class="detail-list">
                            @foreach ($section['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach

            <section class="product-cta-banner">
                <div class="cta-copy">
                    <h3>Let's Discuss Your Requirements</h3>
                    <p>
                        Get pricing and stock information directly from our sales team &mdash; fast response,
                        wholesale pricing, and nationwide delivery.
                    </p>
                </div>
                <div class="cta-actions">
                    <a
                        href="{{ $product['whatsapp'] }}"
                        class="btn btn-whatsapp"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.83 14.12c-.25.7-1.45 1.33-2.02 1.42-.52.08-1.17.11-1.89-.12-.44-.14-1-.32-1.71-.63-3.01-1.3-4.98-4.34-5.13-4.54-.15-.2-1.23-1.63-1.23-3.11 0-1.48.78-2.21 1.05-2.51.28-.3.6-.38.8-.38.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.58.84 2.04.92 2.19.07.15.12.32.02.51-.09.2-.14.32-.28.49-.14.17-.29.38-.42.51-.14.14-.28.29-.12.57.16.28.72 1.19 1.55 1.93 1.06.95 1.96 1.25 2.24 1.39.28.14.44.12.6-.07.17-.2.7-.81.88-1.09.19-.28.38-.23.63-.14.26.09 1.64.77 1.92.91.28.14.47.21.54.33.07.12.07.68-.18 1.38z"
                            />
                        </svg>
                        WhatsApp Consultation
                    </a>
                    <a
                        href="{{ $product['whatsapp'] }}"
                        class="btn btn-ghost-light"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Request a Quote
                    </a>
                </div>
            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-gallery.js') }}"></script>
@endpush
