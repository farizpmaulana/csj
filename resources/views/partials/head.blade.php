@php
    $title ??= config('app.name');
    $description ??= null;
    $canonical ??= null;
    $ogImage ??= asset('images/logo_own/logo_csj02.png');
    $styles ??= [];
    $fontAwesome ??= false;
    $jsonLd ??= null;
@endphp
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}" />
    <meta name="author" content="PT Cahaya Sarange Jaya" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#1e3a8a" />
    @if ($canonical)
        <link rel="canonical" href="{{ $canonical }}" />
    @endif

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="PT Cahaya Sarange Jaya" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}" />
    <meta property="og:image" content="{{ $ogImage }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    @if ($fontAwesome)
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @endif
    @foreach ($styles as $style)
        <link rel="stylesheet" href="{{ asset($style) }}" />
    @endforeach

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}" />

    @if ($jsonLd)
        <script type="application/ld+json">
            {!! json_encode($jsonLd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif
