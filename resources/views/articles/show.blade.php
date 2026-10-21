@php
    $title = $articleModel->seo_title;
    $description = $articleModel->lead;
    $canonical = route('articles.show', ['article' => $articleModel->slug]);
    $ogImage = media($articleModel->hero_image);
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
                src="{{ media($articleModel->hero_image) }}"
                alt="{{ $articleModel->hero_alt }}"
                width="1600"
                height="900"
            />
            <div class="article-detail-hero-copy">
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
                    <p>{{ $articleModel->cta_text }}</p>
                    <a href="{{ wa_link() }}" target="_blank" rel="noopener noreferrer"
                        >Contact Our Team</a
                    >
                </div>
            </article>
        </div>
    </main>
@endsection
