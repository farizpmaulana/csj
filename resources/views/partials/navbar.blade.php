@php
    $variant = $variant ?? 'main';
    $home = route('home');
    $siteSettings = $siteSettings ?? \App\Models\SiteSetting::current();
@endphp
@if ($variant === 'inner')
    <nav class="{{ $innerClass }}">
        <div class="container {{ $innerClass }}-inner">
            <a class="navbar-brand" href="{{ $home }}">
                <div class="logo">
                    <img
                        src="{{ media($siteSettings->logo_path) }}"
                        alt="{{ $siteSettings->company_name }} Logo"
                        class="logo-image"
                        loading="eager"
                        decoding="async"
                        width="180"
                        height="180"
                    />
                </div>
            </a>
            <a class="article-navbar-back" href="{{ $backUrl }}">&larr; {{ $backLabel }}</a>
        </div>
    </nav>  
@else
    <nav class="navbar navbar-expand-lg navbar-light fixed-top {{ request()->routeIs('home') ? 'navbar--hero' : '' }}">
        <div class="container">
            <a class="navbar-brand" href="{{ $home }}#home">
                <div class="logo">
                    <img
                        src="{{ media($siteSettings->logo_path) }}"
                        alt="{{ $siteSettings->company_name }} Logo"
                        class="logo-image {{ request()->routeIs('home') ? 'logo-image--default' : '' }}"
                        loading="eager"
                        decoding="async"
                        fetchpriority="high"
                        width="180"
                        height="180"
                    />
                    @if (request()->routeIs('home'))
                        <img
                            src="{{ media('images/logo_own/logo_csj_white.png') }}"
                            alt=""
                            class="logo-image logo-image--light"
                            loading="eager"
                            decoding="async"
                            width="180"
                            height="180"
                            aria-hidden="true"
                        />
                    @endif
                </div>
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $home }}#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $home }}#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $home }}#partners">Partners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $home }}#gallery">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link {{ ($activeNav ?? null) === 'articles' ? 'active' : '' }}"
                            href="{{ route('articles.index') }}"
                            @if (($activeNav ?? null) === 'articles') aria-current="page" @endif
                        >
                            Articles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $home }}#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endif
