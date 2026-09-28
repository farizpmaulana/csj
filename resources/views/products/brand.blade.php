@php
    $title = $brand['seo_title'];
    $description = $brand['intro'];
    $canonical = route('products.brand', ['brand' => $brandKey]);
    $styles = ['css/product-detail.css', 'css/style.css', 'css/responsive.css'];
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
    <header class="product-hero">
        <div class="container">
            <h1>{{ $brand['name'] }}</h1>
            <p>{{ $brand['headline'] }}</p>
        </div>
    </header>

    <main class="product-content">
        <div class="container">
            <a href="{{ route('home') }}#partners" class="back-to-partners">
                <span class="back-to-partners-icon" aria-hidden="true">&lsaquo;</span>
                Back to Partners
            </a>

            <p class="catalog-intro">{{ $brand['intro'] }}</p>

            @foreach ($brand['categories'] as $category)
                @if ($category['name'])
                    <h2 class="catalog-category-title">{{ $category['name'] }}</h2>
                @endif

                <div class="catalog-grid">
                    @foreach ($category['products'] as $card)
                        @php
                            $productUrl = route('products.show', ['brand' => $brandKey, 'product' => $card['slug']]);
                        @endphp

                        <article class="catalog-card">
                            <div class="catalog-card-image">
                                <a class="catalog-card-image-link" href="{{ $productUrl }}">
                                    <img src="{{ asset($card['image']) }}" alt="{{ $card['alt'] }}" />
                                </a>
                            </div>
                            <div class="catalog-card-body">
                                @if ($card['label'])
                                    <p class="catalog-card-spec">{{ $card['label'] }}</p>
                                @endif
                                <h3 class="catalog-card-title">{{ $card['title'] }}</h3>
                                <p class="catalog-card-spec">{{ $card['excerpt'] }}</p>
                                <div class="catalog-card-actions">
                                    <a
                                        class="catalog-card-btn catalog-card-btn-primary"
                                        href="{{ $products[$card['slug']]['whatsapp'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Request Price
                                    </a>
                                    <a class="catalog-card-btn catalog-card-btn-outline" href="{{ $productUrl }}">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </div>
    </main>
@endsection
