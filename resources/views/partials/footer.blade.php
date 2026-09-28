@php
    $home = route('home');
@endphp
<footer class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h2 class="footer-brand-name">PT Cahaya Sarange Jaya</h2>
                    <div class="footer-social">
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
                        <li>
                            <a href="{{ route('products.brand', ['brand' => 'sumato']) }}">Sumato Fire Extinguishers</a>
                        </li>
                        <li>
                            <a href="{{ route('products.brand', ['brand' => 'bosch']) }}">Bosch Tools</a>
                        </li>
                        <li>
                            <a href="{{ route('products.brand', ['brand' => 'buccheri']) }}">Buccheri Safety Shoes</a>
                        </li>
                        <li>
                            <a href="{{ route('products.brand', ['brand' => 'sata']) }}">SATA Tools</a>
                        </li>
                        <li>
                            <a href="{{ route('products.brand', ['brand' => 'consumables']) }}">Consumables</a>
                        </li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-heading">Contact</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            Jl. MT Haryono Ruko Haryono Palace No.25, Balikpapan,
                            Kalimantan Timur 76125
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            Jl. Cilandak Raya KKO / Komplek Vico Kav. 12, Jakarta Selatan,
                            DKI Jakarta 12560
                        </li>
                        <li>
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                            <a href="https://wa.me/6281140000223" target="_blank" rel="noopener noreferrer"
                                >+62 811 40000 223</a
                            >
                        </li>
                        <li>
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <a href="mailto:info@cahayasarangejaya.co.id">info@cahayasarangejaya.co.id</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p>&copy; {{ date('Y') }} PT Cahaya Sarange Jaya. All rights reserved.</p>
                <a href="#home" class="footer-back-top"
                    ><i class="fas fa-arrow-up" aria-hidden="true"></i> Back to top</a
                >
            </div>
        </div>
    </div>
</footer>
