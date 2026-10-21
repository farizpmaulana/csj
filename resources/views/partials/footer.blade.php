@php
    $home = route('home');
    $siteSettings = $siteSettings ?? \App\Models\SiteSetting::current();
@endphp
<footer class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h2 class="footer-brand-name">{{ $siteSettings->company_name }}</h2>
                    <div class="footer-social">
                        @foreach ([
                            'instagram' => $siteSettings->instagram_url,
                            'facebook' => $siteSettings->facebook_url,
                            'linkedin' => $siteSettings->linkedin_url,
                        ] as $network => $url)
                            @if ($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="Follow us on {{ ucfirst($network) }}">
                                    <i class="fab fa-{{ $network === 'linkedin' ? 'linkedin-in' : $network }}" aria-hidden="true"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="footer-col">
                    <h3 class="footer-heading">Company</h3>
                    <ul class="footer-links">
                        <li><a href="{{ $home }}#home">Home</a></li>
                        <li><a href="{{ $home }}#about">About</a></li>
                        <li><a href="{{ $home }}#partners">Partners</a></li>
                        <li><a href="{{ $home }}#gallery">Gallery</a></li>
                        <li><a href="{{ route('articles.index') }}">Articles</a></li>
                        <li><a href="{{ $home }}#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-heading">Products</h3>
                    <ul class="footer-links">
                        @foreach (\App\Models\Brand::query()->visible()->ordered()->get() as $brand)
                            <li>
                                <a href="{{ route('products.brand', ['brand' => $brand->slug]) }}">{{ $brand->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-heading">Contact</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            {{ $siteSettings->address_primary }}
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            {{ $siteSettings->address_secondary }}
                        </li>
                        <li>
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                            <a href="{{ wa_link() }}" target="_blank" rel="noopener noreferrer"
                                >{{ $siteSettings->contact_phone }}</a
                            >
                        </li>
                        <li>
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <a href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p>&copy; {{ date('Y') }} {{ $siteSettings->company_name }}. All rights reserved.</p>
                <a href="#home" class="footer-back-top"
                    ><i class="fas fa-arrow-up" aria-hidden="true"></i> Back to top</a
                >
            </div>
        </div>
    </div>
</footer>
