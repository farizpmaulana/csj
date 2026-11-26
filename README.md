
# PT Cahaya Sarange Jaya (CSJ) — Web Platform & Content Portal

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-5.x-FDAE4B?style=flat-square&logo=laravel&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Pest](https://img.shields.io/badge/Tested_with-Pest_5-6A5ACD?style=flat-square)](https://pestphp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

Platform web profil perusahaan, katalog produk multi-brand, portal artikel, dan sistem manajemen konten (**CSJ Content Portal**) untuk **PT Cahaya Sarange Jaya**. Dibangun menggunakan **Laravel 13**, **Filament 5**, **Tailwind CSS v4**, dan **Vite**.

---


## Ringkasan Proyek

      Aplikasi ini berfungsi sebagai etalase digital resmi PT Cahaya Sarange Jaya sekaligus panel administrasi terpadu bagi tim internal untuk mengelola konten website tanpa menyentuh kode:

- **Publik / Pengunjung:** Menampilkan profil perusahaan, partner/brand unggulan, katalog produk lengkap dengan spesifikasi teknis, daftar klien, galeri dokumentasi, serta artikel berita yang terhubung langsung ke tombol konsultasi WhatsApp.
- **Admin CMS (`/admin-cms`):** Portal manajemen berbasis Filament 5 yang dilengkapi dashboard ringkas, manajemen user admin, serta _activity log_ untuk melacak setiap perubahan data di CMS.

---

## Fitur Utama

### Website Publik

- **Landing Page (`/`):**
    - Hero section, profil perusahaan, layanan, dan informasi kontak.
    - **Partner & Brand Showcase:** Selektor brand interaktif beserta etalase produk unggulan (_Showcase Products_).
    - **Clients Showcase:** Tampilan logo klien perusahaan yang responsif.
    - **Galeri Foto:** Kartu galeri dengan tampilan _zoom modal_.
- **Katalog Produk (`/products` & `/products/{brand}/{product}`):**
    - Filter katalog berdasarkan brand dan kategori produk.
    - Halaman detail produk dengan galeri gambar, deskripsi _rich text_, tombol pemesanan/konsultasi WhatsApp, dan bagian spesifikasi teknis (_collapsible_).
- **Artikel & Berita (`/articles` & `/articles/{article}`):**
    - Daftar artikel dan halaman detail artikel dengan dukungan _rich content_.
- **SEO & Legacy URL Redirects:**
    - Meta tags dinamis, OpenGraph, Twitter Card, dan JSON-LD schema.
    - Redirect 301 otomatis untuk URL website lama (misalnya path `.html`) melalui `LegacyRedirect` agar peringkat SEO tetap terjaga.

### Admin Content Portal (`/admin-cms`)

_(Route `/admin` otomatis diarahkan ke `/admin-cms`)_

- **Dashboard Ringkas:** Menampilkan statistik utama (**Active Partners**, **Clients**, **Published Products**), ringkasan katalog partner, dan identitas situs.
- **Manajemen Katalog & Konten:**
    - **Brands** & **Product Categories**
    - **Products** (lengkap dengan galeri gambar & bagian spesifikasi teknis) & **Showcase Products**
    - **Clients**, **Gallery Images**, dan **Articles**
    - **Site Settings** (logo, favicon, social image, kontak, dan identitas website)
    - **Legacy Redirects**
- **Manajemen User & Audit Trail:**
    - **Users Management:** Menambah dan mengelola pengguna yang memiliki hak akses ke panel CMS (`is_admin`).
    - **Activity Logs:** Pencatatan otomatis aktivitas perubahan data (_created_, _updated_, _deleted_) untuk melacak siapa mengubah apa di dalam CMS.

---

## Tech Stack

- **Backend:** Laravel `^13.17`, PHP `^8.3`
- **Admin Panel (CMS):** Filament PHP `~5.0`
- **Frontend & Styling:** Blade Templates, Tailwind CSS `^4.0`, Vite `^8.0` (`laravel-vite-plugin`), Custom CSS & JS
- **Database:** SQLite (default untuk pengembangan lokal), MySQL / PostgreSQL (untuk produksi)
- **Testing & Formatting:** Pest `^5.2` (`pest-plugin-laravel`), Laravel Pint `^1.27`

---

## Instalasi Lokal

### 1. Klon Repositori

```bash
git clone <url-repository>
cd csj
```

### 2. Setup Otomatis (Opsional)

Anda dapat menjalankan script `setup` bawaan di `composer.json`:

```bash
composer run setup
```

### 3. Atau Setup Manual Langkah demi Langkah

```bash
# 1. Pasang dependensi PHP & JavaScript
composer install
npm install

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3. Siapkan database SQLite (jika menggunakan default lokal)
touch database/database.sqlite

# 4. Jalankan migrasi dan seeder data awal
php artisan migrate --seed

# 5. Build aset frontend
npm run build
```


### 4. Menjalankan Server Pengembangan

```bash
# Menjalankan server Laravel + Vite secara bersamaan
composer run dev

# Atau di terminal terpisah:
php artisan serve
npm run dev
```

Akses website di `http://127.0.0.1:8000` (atau `http://csj.test` jika menggunakan Laravel Herd) dan panel CMS di `/admin-cms`.

---


## Pengujian & Kualitas Kode


```bash
# Menjalankan seluruh automated test suite (Pest)
php artisan test --compact

# Memformat kode PHP sesuai standar Laravel Pint
vendor/bin/pint
```

---

## Panduan Deployment

### A. Shared Hosting / cPanel (contoh: Rumahweb)

1. Pastikan versi PHP di server adalah **PHP 8.3+**.
2. Jalankan build aset produksi di lokal (`npm run build`) dan pasang dependensi produksi (`composer install --no-dev --optimize-autoloader`).
3. Arahkan **DocumentRoot** domain/subdomain ke folder `public/` (bukan root proyek) agar berkas sensitif seperti `.env`, `storage/`, dan `vendor/` tidak dapat diakses dari browser.
4. Atur variabel `.env` produksi dengan aman:
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://domain-anda.com
    SESSION_SECURE_COOKIE=true
    ```
5. Jalankan `php artisan migrate --force` dan optimasi cache Laravel (`php artisan optimize`).


### B. Vercel (`vercel-php`)

Proyek ini juga menyertakan konfigurasi `vercel.json`, `api/index.php`, dan `.vercelignore` untuk deployment serverless di Vercel:

- Pilih **Framework Preset: Other** pada dashboard Vercel (jangan memilih preset Vite statis).
- Gunakan database eksternal persisten (seperti PostgreSQL di Supabase/Neon atau MySQL cloud) karena filesystem container serverless bersifat sementara (_ephemeral_).
- Isi Environment Variables (`APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, `APP_URL`, serta kredensial `DB_*`) di pengaturan proyek Vercel.

---

## Struktur Direktori Utama

```text
csj/
├── api/
│   └── index.php                       # Entrypoint bridge untuk Vercel serverless
├── app/
│   ├── Filament/
│   │   ├── Pages/                      # Halaman kustom Filament (Dashboard)
│   │   ├── Resources/                  # Resource CMS (Products, Brands, Articles, Users, ActivityLogs, dll.)
│   │   └── Widgets/                    # Widget dashboard CMS
│   ├── Http/Controllers/               # Controller publik (Home, Product, Article, LegacyPage)
│   ├── Models/                         # Eloquent Models & trait LogsActivity
│   ├── Providers/Filament/             # Konfigurasi AdminPanelProvider (/admin-cms)
│   └── Support/                        # Helper & utilitas media
├── database/
│   ├── migrations/                     # Skema tabel database
│   ├── seed-data/                      # Data inisialisasi awal (katalog, media, artikel)
│   └── seeders/                        # Database seeders
├── public/
│   ├── css/                            # Stylesheet kustom
│   ├── js/                             # JavaScript interaktif
│   └── index.php                       # Entrypoint standar Laravel
├── resources/
│   ├── css/                            # Entrypoint Tailwind CSS
│   ├── js/                             # Entrypoint JS Vite
│   └── views/                          # Blade templates & komponen
├── routes/
│   └── web.php                         # Definisi rute publik & redirect /admin -> /admin-cms
├── tests/                              # Automated tests (Pest)
├── composer.json                       # Dependensi PHP & composer scripts
├── package.json                        # Dependensi frontend & Vite scripts
└── vercel.json                         # Konfigurasi deployment Vercel
````

---

## Lisensi

Hak Cipta &copy; PT Cahaya Sarange Jaya.  
Dibangun di atas [Laravel Framework](https://laravel.com), berlisensi [MIT License](https://opensource.org/licenses/MIT).
