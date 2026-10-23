@php
    $title = 'PT Cahaya Sarange Jaya - Company Profile';
    $description = 'PT Cahaya Sarange Jaya - Reliable and experienced general supplier and distributor in the supply chain with a promise to deliver satisfactory exchanges.';
    $canonical = route('home');
    $styles = ['css/style.css', 'css/responsive.css'];
    $fontAwesome = true;

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'PT Cahaya Sarange Jaya',
        'url' => route('home'),
        'logo' => media('images/logo_own/logo_csj02.png'),
        'email' => 'info@cahayasarangejaya.co.id',
        'telephone' => '+62 542 8860262',
        'sameAs' => [
            'https://www.instagram.com/cahayasarangejaya',
            'https://www.facebook.com/ptcahayasarangejaya',
            'https://www.linkedin.com/company/ptcahayasarangejaya',
        ],
        'address' => [
            [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Ruko Puri Mall Fantasi Balikpapan No. 35 Blok A, Damai',
                'addressLocality' => 'Balikpapan',
                'addressRegion' => 'Kalimantan Timur',
                'postalCode' => '76114',
                'addressCountry' => 'ID',
            ],
            [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Jl. Raya Cilandak KKO, Komplek Vico Kav. 12',
                'addressLocality' => 'Jakarta Selatan',
                'addressRegion' => 'DKI Jakarta',
                'postalCode' => '12560',
                'addressCountry' => 'ID',
            ],
        ],
    ];

    $gallerySizes = '(min-width: 992px) 33vw, (min-width: 768px) 50vw, 100vw';
@endphp
@extends('layouts.app')

@section('navbar')
    @include('partials.navbar')
@endsection

@section('content')
        <!-- Home Section -->
        <section id="home" class="hero-section">
          <div class="banner-slider">
            <div class="banner-slide banner-slide--fill" style="--hero-shade: 0.56">
              <picture>
                <source
                  type="image/avif"
                  srcset="
                    {{ media('images/hero/hero1-640.avif') }} 640w,
                    {{ media('images/hero/hero1-960.avif') }} 960w,
                    {{ media('images/hero/hero1-1440.avif') }} 1440w,
                    {{ media('images/hero/hero1-1920.avif') }} 1920w
                  "
                  sizes="100vw"
                />
                <source
                  type="image/webp"
                  srcset="
                    {{ media('images/hero/hero1-640.webp') }} 640w,
                    {{ media('images/hero/hero1-960.webp') }} 960w,
                    {{ media('images/hero/hero1-1440.webp') }} 1440w,
                    {{ media('images/hero/hero1-1920.webp') }} 1920w
                  "
                  sizes="100vw"
                />
                <img
                  src="{{ media('images/hero/hero1-960.jpg') }}"
                  srcset="
                    {{ media('images/hero/hero1-640.jpg') }} 640w,
                    {{ media('images/hero/hero1-960.jpg') }} 960w,
                    {{ media('images/hero/hero1-1440.jpg') }} 1440w,
                    {{ media('images/hero/hero1-1920.jpg') }} 1920w
                  "
                  sizes="100vw"
                  alt="Petugas gudang memeriksa persediaan perlengkapan industri"
                  class="banner-image"
                  width="1920"
                  height="1111"
                  loading="eager"
                  decoding="async"
                  fetchpriority="high"
                />
              </picture>
              <div class="hero-copy">
                <span class="hero-eyebrow">Industrial Supply &amp; Distribution</span>
                <h1>PT. Cahaya Sarange Jaya</h1>
                <p>Safety equipment and industrial products to support your projects and operations.</p>
                <a class="hero-cta" href="#about">Discover More <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
              </div>
            </div>
            <div class="banner-slide banner-slide--fill" style="--hero-shade: 0.24">
              <picture>
                <source
                  type="image/avif"
                  srcset="
                    {{ media('images/hero/hero2-640.avif') }} 640w,
                    {{ media('images/hero/hero2-960.avif') }} 960w,
                    {{ media('images/hero/hero2-1440.avif') }} 1440w,
                    {{ media('images/hero/hero2-1920.avif') }} 1920w
                  "
                  sizes="100vw"
                />
                <source
                  type="image/webp"
                  srcset="
                    {{ media('images/hero/hero2-640.webp') }} 640w,
                    {{ media('images/hero/hero2-960.webp') }} 960w,
                    {{ media('images/hero/hero2-1440.webp') }} 1440w,
                    {{ media('images/hero/hero2-1920.webp') }} 1920w
                  "
                  sizes="100vw"
                />
                <img
                  src="{{ media('images/hero/hero2-960.jpg') }}"
                  srcset="
                    {{ media('images/hero/hero2-640.jpg') }} 640w,
                    {{ media('images/hero/hero2-960.jpg') }} 960w,
                    {{ media('images/hero/hero2-1440.jpg') }} 1440w,
                    {{ media('images/hero/hero2-1920.jpg') }} 1920w
                  "
                  sizes="100vw"
                  alt="Rak penyimpanan produk dan perlengkapan industri di dalam gudang"
                  class="banner-image"
                  width="1920"
                  height="854"
                  loading="lazy"
                  decoding="async"
                  fetchpriority="low"
                />
              </picture>
              <div class="hero-copy">
                <span class="hero-eyebrow">Your Trusted Supply Partner</span>
                <h2>Everything Your Business Needs</h2>
                <p>Dependable products and responsive service for manufacturing, projects, and daily operations.</p>
                <a class="hero-cta" href="#partners">Explore Our Partners <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
              </div>
            </div>

            <!-- Banner Navigation -->
            <div class="banner-nav">
              <button class="banner-nav-btn prev-btn" aria-label="Previous slide">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
              </button>
              <button class="banner-nav-btn next-btn" aria-label="Next slide">
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
              </button>
            </div>

            <!-- Banner Progress Indicators -->
            <div class="banner-progress" role="tablist" aria-label="Banner slides">
              <button
                class="banner-progress-bar active"
                role="tab"
                tabindex="0"
                aria-selected="true"
                aria-label="Go to slide 1"
              >
                <span class="banner-progress-fill"></span>
              </button>
              <button
                class="banner-progress-bar"
                role="tab"
                tabindex="0"
                aria-selected="false"
                aria-label="Go to slide 2"
              >
                <span class="banner-progress-fill"></span>
              </button>
            </div>
          </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about-section">
          <div class="container">
            <div class="about-composition">
              <div class="about-overview">
                <h2 class="section-title about-section-title">About Us</h2>
                <h3 class="about-panel-headline">
                  Sumato, Bosch, Buccheri Industrial Official Distributor
                </h3>
                <p class="about-panel-sub">
                  PT Cahaya Sarange Jaya connects businesses with dependable
                  products from Sumato, Bosch, and Buccheri. We combine quality
                  products, practical product guidance, and responsive service
                  to help procurement and operations teams move with confidence.
                </p>
                <div class="about-stats" aria-label="Company network statistics">
                  <div class="about-stat">
                    <strong data-target="17">0</strong>
                    <span>Total Clients</span>
                  </div>
                  <div class="about-stat">
                    <strong data-target="5">0</strong>
                    <span>Total Partners</span>
                  </div>
                </div>
              </div>

              <div class="about-visual">
                <div class="about-image">
                  <img
                    src="{{ media('images/bg/about_us.webp') }}"
                    alt="Industrial supply and distribution support from PT Cahaya Sarange Jaya"
                    class="about-us-image"
                    loading="lazy"
                    decoding="async"
                    width="3840"
                    height="2154"
                    data-fallback="{{ media('images/bg/about_us.webp') }}"
                  />
                </div>
              </div>
              <div class="about-purpose">
                <div class="about-purpose-card about-purpose-vision">
                  <div class="about-purpose-icon">
                    <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                  </div>
                  <h3 class="about-purpose-label">Our Vision</h3>
                  <p class="about-purpose-text">
                    To be a fast and trustworthy one-stop goods supplier for
                    companies around the world.
                  </p>
                </div>
                <div class="about-purpose-card about-purpose-mission">
                  <div class="about-purpose-icon">
                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                  </div>
                  <h3 class="about-purpose-label">Our Mission</h3>
                  <ul class="about-purpose-list">
                    <li>To distribute the best quality of product to our consumers.</li>
                    <li>To become the choice of global partners for top quality brands.</li>
                    <li>To best bridge the business process between brands and consumers.</li>
                    <li>To conduct trustworthy business ethics.</li>
                  </ul>
                </div>
              </div>
            </div>

            <section id="clients" class="clients-showcase" aria-label="Client companies">
              <div class="clients-showcase-info">
                <p class="clients-showcase-tagline">
                  Trusted by the teams that keep industry moving
                </p>
                <p class="clients-showcase-description">
                  We help businesses stay equipped with dependable products and
                  responsive support.
                </p>
              </div>

              @if ($clients->isNotEmpty())
                <div class="clients-showcase-content">
                  <div class="clients-marquee clients-showcase-marquee" role="group" aria-label="Client logos">
                    <div id="clients-marquee" class="clients-track" role="list" aria-label="Client companies" aria-live="off">
                      @foreach ($clients as $client)
                        <div class="client-logo" role="listitem">
                          <div class="client-logo-img">
                            <img
                              src="{{ $client->url() }}"
                              srcset="{{ $client->variantSrcset() }}"
                              sizes="(min-width: 1200px) 160px, (min-width: 768px) 28vw, 45vw"
                              alt="{{ $client->alt }}"
                              class="client-logo-image img-fade"
                              loading="lazy"
                              decoding="async"
                              width="160"
                              height="100"
                            />
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div
                    id="clients-static-list"
                    class="clients-static"
                    data-client-static-list
                    role="list"
                    aria-label="All client companies"
                    hidden
                  ></div>
                  <div class="clients-mode-actions">
                    <button
                      type="button"
                      class="clients-mode-link"
                      data-client-show-all
                      aria-expanded="false"
                      aria-controls="clients-static-list"
                    >
                      See all clients <span aria-hidden="true">&rarr;</span>
                    </button>
                    <button
                      type="button"
                      class="clients-mode-link"
                      data-client-show-carousel
                      aria-expanded="true"
                      aria-controls="clients-static-list"
                      hidden
                    >
                      Back to carousel <span aria-hidden="true">&larr;</span>
                    </button>
                  </div>
                </div>
              @else
                <div class="clients-showcase-content">
                  <p class="clients-empty">Our client logos will be featured here soon.</p>
                </div>
              @endif
            </section>
          </div>
        </section>

        <!-- Partners Section -->
        <section
          id="partners"
          class="partners-section"
          data-bg-lazy="{{ media('images/bg/background-navy-07.webp') }}"
        >
          <div class="container">
            <div class="partners-intro">
              <h2 class="section-title">Our Partners</h2>
              <p class="partners-description">
                We are proud to work with trusted partners as an official distributor of quality products for industrial needs.
              </p>
            </div>

            <div class="partners-brand-selectors" role="group" aria-label="Filter product showcase by brand">
              @foreach ($brands as $partner)
                <button
                  class="partners-brand-selector"
                  type="button"
                  data-partner-brand-filter="{{ $partner->slug }}"
                  aria-pressed="false"
                  aria-label="Show {{ $partner->name }} products"
                >
                  <img
                    class="partners-brand-logo {{ in_array($partner->slug, ['bosch', 'sata', 'sumato', 'buccheri'], true) ? 'partners-brand-logo-wide' : 'partners-brand-logo-compact' }}"
                    src="{{ media($partner->card_image) }}"
                    alt=""
                    loading="lazy"
                    decoding="async"
                  />
                </button>
              @endforeach
            </div>

            <div class="partner-product-showcase" data-partner-product-showcase>
              <div class="partner-product-showcase-heading">
                <div>
                  <h3>A closer look at products from our partners</h3>
                </div>
                <div class="partner-product-showcase-actions">
                  <div class="partner-product-carousel-controls" data-partner-product-controls>
                    <button type="button" data-partner-product-previous aria-label="Previous products" disabled>
                      <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m12.5 4.5-5.5 5.5 5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                    <button type="button" data-partner-product-next aria-label="Next products" disabled>
                      <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </button>
                  </div>
                </div>
              </div>

              @if ($partnerProducts->isNotEmpty())
                <div class="partner-product-grid" data-partner-product-track role="list" aria-label="Products">
                  @php
                    $brandProductIndexes = [];
                  @endphp
                  @foreach ($partnerProducts as $product)
                    @php
                      $brandProductIndex = $brandProductIndexes[$product->brand_id] ?? 0;
                      $brandProductIndexes[$product->brand_id] = $brandProductIndex + 1;
                      $showByDefault = in_array($product->id, $initialShowcaseProductIds, true);
                    @endphp
                    <a
                      class="partner-product-card"
                      href="{{ $product->url }}"
                      data-product-brand="{{ $product->brand->slug }}"
                      data-product-index="{{ $brandProductIndex }}"
                      {{ $showByDefault ? '' : 'hidden' }}
                      role="listitem"
                      aria-label="{{ $product->name }} by {{ $product->brand->name }}"
                    >
                      <span class="partner-product-card-image">
                        <img
                          src="{{ media($product->card_image) }}"
                          alt="{{ $product->card_alt }}"
                          width="480"
                          height="320"
                          loading="lazy"
                          decoding="async"
                        />
                        <span class="partner-product-brand-badge">
                          <img
                            class="partner-product-brand-logo {{ in_array($product->brand->slug, ['bosch', 'sata', 'sumato', 'buccheri'], true) ? 'partner-product-brand-logo-wide' : 'partner-product-brand-logo-compact' }}"
                            src="{{ media($product->brand->card_image) }}"
                            alt=""
                            loading="lazy"
                            decoding="async"
                          />
                        </span>
                        <span class="partner-product-card-arrow" aria-hidden="true">
                          <svg viewBox="0 0 20 20" fill="none">
                            <path d="M5 15 15 5M6 5h9v9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                        </span>
                      </span>
                      <span class="partner-product-card-title">{{ $product->name }}</span>
                    </a>
                  @endforeach
                </div>
                <p class="partner-product-empty" data-partner-product-empty hidden>
                  No products are currently listed for this brand.
                </p>
              @else
                <p class="partner-product-empty">
                  Products will be added to the showcase soon.
                </p>
              @endif
              <div class="partner-product-showcase-footer">
                <a class="partner-product-showcase-link" href="{{ route('products.index') }}">
                  <span class="partner-product-showcase-link-label">Browse full catalog</span>
                  <span aria-hidden="true">&rarr;</span>
                </a>
              </div>
            </div>
          </div>
        </section>

        <!-- Why Choose Us Section -->
        <section id="why-us" class="why-us-section">
          <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <p class="section-intro">
              We Help Procurement and Operations Teams Secure the Right Supplies
              Quickly and Reliably.
            </p>
            <div class="row">
              <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="value-card">
                  <div class="value-icon"><i class="fas fa-bolt"></i></div>
                  <h3>Quick Response</h3>
                  <p>
                    Inquiries and quotations are addressed promptly, ensuring timely
                    service.
                  </p>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="value-card">
                  <div class="value-icon"><i class="fas fa-shield-alt"></i></div>
                  <h3>Trusted Products</h3>
                  <p>
                    Provide high-quality products to meet your industrial needs.
                  </p>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="value-card">
                  <div class="value-icon"><i class="fas fa-truck-fast"></i></div>
                  <h3>Time Delivery</h3>
                  <p>
                    Dependable shipping to ensure optimal efficiency of your
                    operations.
                  </p>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="value-card">
                  <div class="value-icon"><i class="fas fa-handshake"></i></div>
                  <h3>Long-Term Partnership</h3>
                  <p>
                    Sustaining valuable business relationships through reliable and
                    professional service.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- CTA Strip -->
        <section class="cta-strip-section">
          <div class="container">
            <div class="cta-strip">
              <div class="cta-strip-text">
                <h3>Need a quick quote for your project or operational needs?</h3>
                <p>
                  Our team is ready to help you choose the right products for your
                  requirements.
                </p>
              </div>
              <div class="cta-strip-actions">
                <a href="#contact" class="cta-strip-btn cta-strip-btn-primary"
                  >Contact Us</a
                >
                <a
                  href="https://wa.me/6281140000223"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="cta-strip-btn cta-strip-btn-secondary"
                  >Chat WhatsApp</a
                >
              </div>
            </div>
          </div>
        </section>

        <!-- Gallery Section -->
        <section
          id="gallery"
          class="gallery-section"
          data-bg-lazy="{{ media('images/bg/background-navy-07.webp') }}"
        >
          <div class="container">
            <h2 class="section-title">Gallery</h2>
            <div class="row">
              @forelse ($gallery as $image)
                <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="gallery-item">
                    <div class="gallery-image-container">
                      <picture>
                        @foreach ($image->sourceSrcsets() as $type => $srcset)
                          <source type="{{ $type }}" srcset="{{ $srcset }}" sizes="{{ $gallerySizes }}" />
                        @endforeach
                        <img
                          src="{{ $image->url() }}"
                          srcset="{{ $image->variantSrcset() }}"
                          sizes="{{ $gallerySizes }}"
                          alt="{{ $image->alt }}"
                          class="gallery-image img-fade"
                          width="{{ $image->width }}"
                          height="{{ $image->height }}"
                          loading="lazy"
                          decoding="async"
                        />
                      </picture>
                      <div class="gallery-overlay">
                        @if ($image->caption)
                          <p class="gallery-caption">{{ $image->caption }}</p>
                        @endif
                        <button
                          type="button"
                          class="gallery-zoom-button"
                          data-gallery-image="{{ $image->fullUrl() }}"
                          aria-label="View {{ $image->alt }} fullscreen"
                        >
                          <i class="fas fa-search-plus" aria-hidden="true"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="col-12"><p class="gallery-empty">No gallery images yet.</p></div>
              @endforelse
            </div>
          </div>
        </section>

        <div
          class="modal fade"
          id="galleryModal"
          tabindex="-1"
          aria-labelledby="galleryModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
              <div class="modal-header border-0">
                <h2 class="visually-hidden" id="galleryModalLabel">
                  Gallery image preview
                </h2>
                <button
                  type="button"
                  class="btn-close btn-close-white ms-auto"
                  data-bs-dismiss="modal"
                  aria-label="Close image preview"
                ></button>
              </div>
              <div class="modal-body p-0 text-center">
                <img
                  id="galleryModalImage"
                  class="gallery-modal-image img-fluid rounded"
                  src=""
                  alt=""
                  width="1200"
                  height="800"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Articles Section -->
        <section id="recent-articles" class="recent-articles-section">
          <div class="container">
            <div class="recent-articles-heading">
              <div>
                <h2 class="section-title">Recent Articles</h2>
              </div>
              <a class="recent-articles-view-all" href="{{ route('articles.index') }}">
                View all articles <span aria-hidden="true">&rarr;</span>
              </a>
            </div>

            <div class="recent-articles-grid">
              @foreach ($latestArticles as $article)
                <a
                  class="recent-article-card"
                  href="{{ route('articles.show', ['article' => $article->slug]) }}"
                >
                  <div class="recent-article-image">
                    <img
                      src="{{ media($article->card_image) }}"
                      alt="{{ $article->card_alt }}"
                      loading="lazy"
                      decoding="async"
                      width="900"
                      height="506"
                    />
                  </div>
                  <div class="recent-article-body">
                    <div class="recent-article-meta">
                      <span>{{ $article->category_label }}</span>
                      <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_label }}</time>
                    </div>
                    <h3>{{ $article->title }}</h3>
                    <p>{{ $article->excerpt }}</p>
                    <span class="recent-article-link">
                      Read more <span aria-hidden="true">&rarr;</span>
                    </span>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        </section>

        <!-- Contact Section -->
        <section
          id="contact"
          class="contact-section"
          data-bg-lazy="{{ media('images/bg/background-navy-07.webp') }}"
        >
          <div class="container">
            <h2 class="section-title">Find Us</h2>

            <div class="row g-5">
              <div class="col-lg-8">
                <div class="offices-grid">
                  <div class="office-block">
                    <div class="office-block-header">
                      <div class="office-block-icon">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                      </div>
                      <div class="office-block-title">
                        <h3>Balikpapan Office</h3>
                      </div>
                    </div>
                    <p class="office-address">
                      Jl. MT Haryono Ruko Haryono Palace No.25, Gn. Bahagia,
                      Kecamatan Balikpapan Selatan, Kota Balikpapan, Kalimantan
                      Timur 76125
                    </p>
                    <div class="map-container">
                      <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1434.936375326886!2d116.87296494246975!3d-1.2371687085888792!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df147f03e3624eb%3A0xb5ef50e7aa6531c5!2sPT%20Cahaya%20Sarange%20Jaya!5e0!3m2!1sen!2sid!4v1786413702230!5m2!1sen!2sid"
                        width="100%"
                        height="200"
                        style="border: 0"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Balikpapan Office Location"
                      >
                      </iframe>
                    </div>
                    <div class="office-block-footer">
                      <a
                        href="https://www.google.com/maps/dir/?api=1&destination=PT+Cahaya+Sarange+Jaya+Balikpapan"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="office-directions-link"
                      >
                        <i class="fas fa-directions" aria-hidden="true"></i>
                        Get Directions
                      </a>
                    </div>
                  </div>
                  <div class="office-block">
                    <div class="office-block-header">
                      <div class="office-block-icon">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                      </div>
                      <div class="office-block-title">
                        <h3>Jakarta Office</h3>
                      </div>
                    </div>
                    <p class="office-address">
                      Jl. Cilandak Raya KKO / Komplek Vico Kav. 12, Cilandak Tim.,
                      Ps. Minggu, Kota Jakarta Selatan, Daerah Khusus Ibukota
                      Jakarta 12560
                    </p>
                    <div class="map-container">
                      <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3005.4046742606224!2d106.81031663227196!3d-6.3089517830394355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ef8241f81af3%3A0x716f6cd55fcce576!2sPT%20Cahaya%20Sarange%20Jaya!5e0!3m2!1sen!2sid!4v1788341628634!5m2!1sen!2sid"
                        width="100%"
                        height="200"
                        style="border: 0"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        title="Jakarta Office Location"
                      ></iframe>
                    </div>
                    <div class="office-block-footer">
                      <a
                        href="https://www.google.com/maps/dir/?api=1&destination=PT+Cahaya+Sarange+Jaya+Jakarta"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="office-directions-link"
                      >
                        <i class="fas fa-directions" aria-hidden="true"></i>
                        Get Directions
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-4 contact-side">
                <div class="contact-list">
                  <a
                    class="contact-item"
                    href="https://wa.me/6281140000223"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <div class="contact-item-icon">
                      <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    </div>
                    <div class="contact-item-body">
                      <span class="contact-item-label">WhatsApp</span>
                      <span class="contact-item-value">+62 811 40000 223</span>
                    </div>
                  </a>
                  <a
                    class="contact-item"
                    href="mailto:info@cahayasarangejaya.co.id"
                  >
                    <div class="contact-item-icon">
                      <i class="fas fa-envelope" aria-hidden="true"></i>
                    </div>
                    <div class="contact-item-body">
                      <span class="contact-item-label">Email</span>
                      <span class="contact-item-value"
                        >info@cahayasarangejaya.co.id</span
                      >
                    </div>
                  </a>
                </div>

                <div class="contact-social">
                  <span class="contact-social-title">Follow us</span>
                  <div class="contact-social-icons">
                    <a
                      href="https://www.instagram.com/cahayasarangejaya"
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label="Follow us on Instagram"
                    >
                      <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a
                      href="https://www.facebook.com/ptcahayasarangejaya"
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label="Follow us on Facebook"
                    >
                      <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </a>
                    <a
                      href="https://www.linkedin.com/company/pt-cahayasarangejaya/"
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label="Follow us on LinkedIn"
                    >
                      <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
    
@endsection

@section('footer')
    @include('partials.footer')
    @include('partials.floating-whatsapp')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
@endpush
