@php
    $title = 'Articles - PT Cahaya Sarange Jaya';
    $description = 'Latest information and news on industrial equipment, workplace safety, and other trending topics from PT Cahaya Sarange Jaya.';
    $canonical = route('articles.index');
    $styles = ['css/style.css', 'css/responsive.css', 'css/article.css'];
    $fontAwesome = true;

    $filters = ['all' => 'All'];

    foreach (config('articles') as $article) {
        $filters[$article['category']] ??= $article['category_label'];
    }
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar', ['activeNav' => 'articles'])
@endsection

@section('content')
    <header class="article-hero">
        <div class="container">
            <h1>Articles &amp; News</h1>
            <p>
                Up-to-date information on industrial equipment, workplace safety, and other relevant topics
                to support your business operations.
            </p>
        </div>
    </header>

    <main class="article-section">
        <div class="container">
            <div class="article-filters" role="tablist">
                @foreach ($filters as $filterKey => $filterLabel)
                    <button
                        class="article-filter {{ $loop->first ? 'active' : '' }}"
                        data-filter="{{ $filterKey }}"
                        type="button"
                    >
                        {{ $filterLabel }}
                    </button>
                @endforeach
            </div>

            <div class="article-grid" id="articleGrid">
                @foreach ($articles as $articleSlug => $article)
                    <a
                        class="article-card {{ $article['featured'] ? 'article-card-featured' : '' }}"
                        data-category="{{ $article['category'] }}"
                        href="{{ route('articles.show', ['article' => $articleSlug]) }}"
                    >
                        <div class="article-card-image">
                            <img
                                src="{{ asset($article['card_image']) }}"
                                alt="{{ $article['card_alt'] }}"
                                loading="lazy"
                                decoding="async"
                                width="900"
                                height="506"
                            />
                        </div>
                        <div class="article-card-body">
                            <div class="article-card-meta">
                                <span class="article-card-tag">{{ $article['category_label'] }}</span
                                ><span>{{ $article['published_label'] }}</span>
                            </div>
                            <h3 class="article-card-title">{{ $article['title'] }}</h3>
                            <p class="article-card-excerpt">{{ $article['excerpt'] }}</p>
                            <span class="article-card-link">Read more &rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.article-filter').forEach((button) => {
            button.addEventListener('click', function () {
                document
                    .querySelectorAll('.article-filter')
                    .forEach((item) => item.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                const cards = document.querySelectorAll('#articleGrid .article-card');
                let visible = 0;

                cards.forEach((card) => {
                    const matches =
                        filter === 'all' || card.getAttribute('data-category') === filter;
                    card.style.display = matches ? '' : 'none';
                    if (matches) visible++;
                });

                const grid = document.getElementById('articleGrid');
                let emptyMessage = grid.querySelector('.article-empty');

                if (visible === 0) {
                    if (!emptyMessage) {
                        emptyMessage = document.createElement('div');
                        emptyMessage.className = 'article-empty';
                        emptyMessage.textContent = 'No articles in this category yet.';
                        grid.appendChild(emptyMessage);
                    }
                } else if (emptyMessage) {
                    emptyMessage.remove();
                }
            });
        });
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
@endpush
