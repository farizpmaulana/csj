@php
    $title = $article['seo_title'];
    $description = $article['lead'];
    $canonical = route('articles.show', ['article' => $articleSlug]);
    $ogImage = asset($article['hero_image']);
    $styles = ['css/article.css'];
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar', [
        'variant' => 'inner',
        'innerClass' => 'article-navbar',
        'backUrl' => route('articles.index'),
        'backLabel' => 'Back to Articles',
    ])
@endsection

@section('content')
    <header class="article-detail-hero">
        <div class="container">
            <img
                class="article-detail-hero-image"
                src="{{ asset($article['hero_image']) }}"
                alt="{{ $article['hero_alt'] }}"
                width="1600"
                height="900"
            />
            <div class="article-detail-hero-copy">
                <div class="article-detail-meta">
                    <span class="article-detail-tag">{{ $article['category_label'] }}</span>
                    <span>{{ $article['published_label'] }}</span>
                    <span>{{ $article['reading_time'] }}</span>
                </div>
                <h1>{{ $article['title'] }}</h1>
                <p>{{ $article['lead'] }}</p>
            </div>
        </div>
    </header>

    <main class="article-detail-body">
        <div class="container">
            <article class="article-detail-content">
                {!! $article['body'] !!}

                <div class="article-detail-cta">
                    <p>{{ $article['cta_text'] }}</p>
                    <a href="https://wa.me/6281140000223" target="_blank" rel="noopener noreferrer"
                        >Contact Our Team</a
                    >
                </div>
            </article>
        </div>
    </main>
@endsection
