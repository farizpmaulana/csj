@php
    $title = $articleModel->seo_title;
    $description = $articleModel->lead;
    $canonical = route('articles.show', ['article' => $articleModel->slug]);
    $ogImage = media($articleModel->hero_image);
    $styles = ['css/style.css', 'css/responsive.css', 'css/article.css'];
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar', ['activeNav' => 'articles'])
@endsection

@section('content')
    <header class="article-detail-hero">
        <img
            class="article-detail-hero-image"
            src="{{ media($articleModel->hero_image) }}"
            alt="{{ $articleModel->hero_alt }}"
            width="1600"
            height="900"
        />
        <div class="container">
            <div class="article-detail-hero-copy">
                <a class="article-detail-back article-detail-back-top" href="{{ route('articles.index') }}">
                    <span aria-hidden="true">&larr;</span> Back to Articles
                </a>
                <div class="article-detail-meta">
                    <span class="article-detail-tag">{{ $articleModel->category_label }}</span>
                    <span>{{ $articleModel->published_label }}</span>
                    <span>{{ $articleModel->reading_time }}</span>
                </div>
                <h1>{{ $articleModel->title }}</h1>
                <p>{{ $articleModel->lead }}</p>
            </div>
        </div>
    </header>

    <main class="article-detail-body">
        <div class="container">
            <article class="article-detail-content">
                {!! $articleModel->body !!}

                <div class="article-detail-cta">
                    <div class="article-detail-cta-copy">
                        <span class="article-detail-cta-eyebrow">Talk to our team</span>
                        <h2>Need help with your industrial requirements?</h2>
                        <p>{{ $articleModel->cta_text }}</p>
                    </div>
                    <a href="{{ wa_link() }}" target="_blank" rel="noopener noreferrer">
                        Contact Our Team <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </article>
            <a class="article-detail-back article-detail-back-bottom" href="{{ route('articles.index') }}">
                <span aria-hidden="true">&larr;</span> Back to Articles
            </a>
        </div>
    </main>
@endsection
