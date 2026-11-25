<x-filament-widgets::widget>
    <x-filament::section>
        <div>
            <div class="csj-panel-header">
                <div>
                    <h3 class="csj-panel-title">Site Identity &amp; Quick Links</h3>
                    <p class="csj-panel-subtitle">
                        Active contact &amp; website configuration.
                    </p>
                </div>
                <a href="{{ $settingsUrl }}" class="csj-panel-link">
                    Edit
                </a>
            </div>

            <dl class="csj-dl">
                <div class="csj-dl-row">
                    <dt>Company</dt>
                    <dd>{{ $settings->company_name ?: '—' }}</dd>
                </div>
                <div class="csj-dl-row">
                    <dt>WhatsApp</dt>
                    <dd>{{ $settings->whatsapp_number ?: '—' }}</dd>
                </div>
                <div class="csj-dl-row">
                    <dt>Phone</dt>
                    <dd>{{ $settings->contact_phone ?: '—' }}</dd>
                </div>
                <div class="csj-dl-row">
                    <dt>Email</dt>
                    <dd>{{ $settings->contact_email ?: '—' }}</dd>
                </div>
            </dl>

            <div class="csj-quick-links">
                <p class="csj-quick-heading">Quick Sections</p>
                <div class="csj-quick-list">
                    <a href="{{ $clientsUrl }}" class="csj-quick-item">
                        <span>Client List</span>
                        <x-heroicon-o-chevron-right aria-hidden="true" />
                    </a>
                    <a href="{{ $galleryUrl }}" class="csj-quick-item">
                        <span>Gallery Images</span>
                        <x-heroicon-o-chevron-right aria-hidden="true" />
                    </a>
                    <a href="{{ $categoriesUrl }}" class="csj-quick-item">
                        <span>Product Categories</span>
                        <x-heroicon-o-chevron-right aria-hidden="true" />
                    </a>
                    <a href="{{ $redirectsUrl }}" class="csj-quick-item">
                        <span>Legacy Redirects</span>
                        <x-heroicon-o-chevron-right aria-hidden="true" />
                    </a>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
