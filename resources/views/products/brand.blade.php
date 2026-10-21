@php
    $title = $brandModel->seo_title;
    $description = $brandModel->intro;
    $canonical = route('products.brand', ['brand' => $brandModel->slug]);
    $styles = ['css/product-detail.css', 'css/style.css', 'css/responsive.css'];
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
    <header class="product-hero">
        <div class="container">
            <h1>{{ $brandModel->name }}</h1>
            <p>{{ $brandModel->headline }}</p>
        </div>
    </header>

    <main class="product-content">
        <div class="container">
            <a href="{{ route('home') }}#partners" class="back-to-partners">
                <span class="back-to-partners-icon" aria-hidden="true">&lsaquo;</span>
                Back to Partners
            </a>

            <p class="catalog-intro">{{ $brandModel->intro }}</p>

            @foreach ($brandModel->categories as $category)
                @if ($category->name)
                    <h2 class="catalog-category-title">{{ $category->name }}</h2>
                @endif

                <div class="catalog-grid">
                    @forelse ($category->products as $product)
                        @include('products.partials.card', ['product' => $product])
                    @empty
                        <p class="catalog-empty">No products in this category yet.</p>
                    @endforelse
                </div>
            @endforeach

            @if ($brandModel->uncategorisedProducts->isNotEmpty())
                <div class="catalog-grid">
                    @foreach ($brandModel->uncategorisedProducts as $product)
                        @include('products.partials.card', ['product' => $product])
                    @endforeach
                </div>
            @endif
        </div>
    </main>
@endsection
