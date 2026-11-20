# PT Cahaya Sarange Jaya (CSJ) - Web Platform

Aplikasi web profil perusahaan dan katalog produk resmi **PT Cahaya Sarange Jaya**, dibangun menggunakan **Laravel**, **Filament PHP**, **Tailwind CSS**, dan **Vite**.

---

## 🚀 Fitur Utama

- **Halaman Utama (Landing Page):**
  - Section Hero, Tentang Perusahaan, Layanan, dan Kontak.
  - **Showcase & Selector Partner/Brand:** Interaktif dengan filter katalog produk per brand (Bosch, Sata, Sumato, Buccheri, dll.).
  - **Daftar Klien (Clients Showcase):** Animasi marquee dan logo grid responsif.
  - **Galeri Foto:** Kartu gambar dengan gradient overlay dan modal zoom view.
- **Katalog Produk:**
  - Halaman katalog lengkap (`/products`) dengan filter brand dan kategori.
  - Halaman detail produk (`/products/{brand}/{product}`) dengan galeri gambar dan tabel spesifikasi teknis.
- **Artikel & Berita:**
  - Halaman daftar artikel (`/articles`) dan baca artikel (`/articles/{article}`).
- **SEO & Legacy URL Handling:**
  - Redirect otomatis dari URL website lama bertipe `.html` ke URL modern untuk menjaga skor SEO.
  - Meta tags dinamis, OpenGraph, Twitter Card, dan JSON-LD schema.
- **Admin Dashboard (Filament 5):**
  - Manajemen Pengaturan Situs (Site Settings), Brand, Kategori Produk, Produk & Gambar, Klien, Galeri, dan Artikel.
  - Akses aman melalui `/admin`.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 11/12+, PHP 8.3+
- **Admin Panel:** Filament PHP ~5.0
- **Frontend / Styling:** Blade, Tailwind CSS v4, Custom CSS, Bootstrap 5 (komponen tertentu)
- **Animasi & Interaktivitas:** GSAP & ScrollTrigger
- **Build Tool:** Vite 8, `laravel-vite-plugin`
- **Database:** SQLite (default/lokal), MySQL / PostgreSQL (produksi)

---

## 💻 Panduan Instalasi Lokal

### 1. Klon Repositori
```bash
git clone <url-repository>
cd csj
```

### 2. Pasang Dependensi
```bash
# Dependensi PHP
composer install

# Dependensi JavaScript/CSS
npm install
```

### 3. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi `.env.example`:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database & Migrasi
Secara default, aplikasi menggunakan database SQLite:
```bash
# Pastikan file SQLite ada jika menggunakan SQLite
touch database/database.sqlite

# Jalankan migrasi dan seeder
php artisan migrate --seed
```

### 5. Jalankan Aplikasi
Jalankan server pengembangan Laravel dan Vite secara bersamaan:
```bash
# Cara 1: Menggunakan composer dev (menjalankan artisan serve + vite build watch)
composer run dev

# Atau Cara 2: Di terminal terpisah
php artisan serve
npm run dev
```

Buka peramban di `http://127.0.0.1:8000`.

---

## ☁️ Panduan Deployment ke Vercel

Proyek ini telah dikonfigurasi untuk dapat di-*deploy* langsung ke **Vercel** menggunakan serverless PHP runtime (`vercel-php`).

### Mengapa Muncul Error `[STATIC_BUILD_NO_OUT_DIR]`?
Secara default, Vercel mendeteksi `package.json` yang berisi Vite dan menganggap proyek ini adalah SPA/Static Site murni dengan direktori output `dist`. Pada Laravel, asset dikompilasi ke `public/build`, dan aplikasi membutuhkan PHP runtime untuk mengeksekusi routing, Blade, dan database.

### Berkas Konfigurasi Vercel yang Disediakan
1. **`vercel.json`**:
   - Menetapkan `"framework": null` agar Vercel tidak memaksakan preset static Vite.
   - Mengatur build command `npm run build` dan output static ke direktori `public`.
   - Mengalihkan eksekusi kode PHP ke runtime serverless `vercel-php@0.9.0` melalui `api/index.php`.
   - Mengarahkan asset statis (`/build/*`, `/css/*`, `/js/*`, `/fonts/*`, `/images/*`, dll.) langsung ke `public`.
   - Menetapkan direktori cache dan view Laravel ke `/tmp` (karena filesystem serverless Vercel bersifat *read-only* kecuali folder `/tmp`).
2. **`api/index.php`**:
   - Bridge entrypoint untuk runtime serverless Vercel yang secara otomatis membuat struktur folder yang dibutuhkan di `/tmp/storage`.
3. **`.vercelignore`**:
   - Mengabaikan folder lokal berat (`/vendor`, `/node_modules`, `.env`, dll.) dari upload.

---

### Langkah-langkah Deploy ke Vercel:

#### 1. Push Perubahan ke Git (GitHub / GitLab / Bitbucket)
```bash
git add .
git commit -m "Configure Vercel deployment with vercel-php and update README"
git push origin main
```

#### 2. Import Project di Vercel Dashboard
1. Buka [Vercel Dashboard](https://vercel.com/) dan pilih **Add New...** -> **Project**.
2. Pilih repository proyek ini.
3. Pada bagian **Framework Preset**, pilih **Other** (jangan pilih Vite).
4. Biarkan **Root Directory** tetap `./`.
5. Di bagian **Build & Development Settings**:
   - **Build Command**: Tetap default atau `npm run build`.
   - **Output Directory**: Tetap default (dikelola oleh `vercel.json`).

#### 3. Konfigurasi Environment Variables di Vercel
Tambahkan variabel lingkungan berikut di menu **Settings -> Environment Variables** pada project Vercel:

| Variable | Nilai Rekomendasi | Keterangan |
| :--- | :--- | :--- |
| `APP_NAME` | `PT Cahaya Sarange Jaya` | Nama aplikasi |
| `APP_ENV` | `production` | Lingkungan aplikasi |
| `APP_KEY` | `base64:...` | Ambil dari `.env` lokal Anda atau `php artisan key:generate --show` |
| `APP_DEBUG` | `false` | Nonaktifkan debug di production |
| `APP_URL` | `https://your-domain.vercel.app` | URL domain Vercel Anda |
| `DB_CONNECTION` | `mysql` / `pgsql` | Koneksi database produksi |
| `DB_HOST` | `aws.connect.psdb.cloud` / host DB Anda | Host database cloud |
| `DB_PORT` | `3306` / `5432` | Port database |
| `DB_DATABASE` | `<nama_database>` | Nama database |
| `DB_USERNAME` | `<username_database>` | User database |
| `DB_PASSWORD` | `<password_database>` | Password database |

> [!IMPORTANT]
> **Catatan Database di Vercel:**
> Serverless function di Vercel bersifat *stateless* dan *ephemeral* (sementara). File SQLite lokal di dalam container serverless akan di-reset setiap kali fungsi tidur/dihidupkan ulang. Oleh karena itu, untuk data yang persisten (katalog produk, artikel, user admin), sangat disarankan menggunakan database cloud eksternal seperti **Supabase (PostgreSQL)**, **Neon**, **PlanetScale**, **Railway**, atau **AWS RDS**.

---

## 📁 Struktur Direktori Utama

```
csj/
├── api/
│   └── index.php              # Serverless entrypoint bridge untuk Vercel
├── app/
│   ├── Http/Controllers/     # Controller (Home, Product, Article, Legacy)
│   ├── Models/               # Eloquent Models (Brand, Product, Client, Gallery, dll.)
│   └── Support/              # Helper functions & media utilities
├── database/
│   ├── migrations/           # Skema tabel database
│   ├── seed-data/            # Data inisialisasi awal (katalog, media, artikel)
│   └── seeders/              # Database seeders
├── public/
│   ├── build/                # Aset terkompilasi Vite (manifest, css, js chunks)
│   ├── css/                  # Stylesheet kustom (style.css, responsive.css)
│   ├── js/                   # Javascript interaktif (script.js)
│   └── index.php             # Standar Laravel entrypoint
├── resources/
│   ├── css/                  # Tailwind CSS entrypoint
│   ├── js/                   # JS entrypoint
│   └── views/                # Blade templates & components
├── routes/
│   └── web.php               # Rute web aplikasi
├── vercel.json               # Konfigurasi deployment Vercel
├── vite.config.js            # Konfigurasi Vite
└── composer.json             # Dependensi dan script PHP
```

---

## 📜 Lisensi

Hak Cipta &copy; PT Cahaya Sarange Jaya. Seluruh hak cipta dilindungi undang-undang.
Proyek ini dibangun di atas [Laravel Framework](https://laravel.com), berlisensi [MIT License](https://opensource.org/licenses/MIT).
