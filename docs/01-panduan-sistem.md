# 01 — Panduan Sistem & Arsitektur Teknis — Pojok Berkah

> 🌟 **DOKUMEN RUJUKAN UTAMA SISTEM**: Berlaku penuh per Oktober 2026.
> Dokumen ini merangkum seluruh arsitektur teknis, pola Server-Side Rendering (SSR), Zero-JavaScript, peta struktur direktori, skema data storage, proteksi keamanan, dan optimasi SEO.

---

## 1. Ringkasan Arsitektur

Pojok Berkah adalah aplikasi web publik berbasis HTML5/PHP dengan CMS modular tanpa database SQL dan berarsitektur **100% Zero-JavaScript di sisi browser** (tidak ada JavaScript yang dieksekusi oleh klien publik). Seluruh data dinamis disuntikkan langsung di sisi server sebelum dikirimkan ke browser pengguna.

- **Frontend Publik:** HTML5 semantik, CSS murni (`landing.css`, `effects.css`, `legal.css`), tanpa framework JavaScript eksternal.
- **Backend CMS:** PHP modular di folder `admin/`, berbasis session aman, autentikasi native, tanpa framework, tanpa Composer.
- **Penyimpanan Data:** File privat tunggal `data/storage.php` yang diproteksi `.htaccess` (tidak pernah diekspos ke publik atau di-commit ke Git).
- **Kompatibilitas:** PHP 7.4 hingga PHP 8.2+ (diuji tanpa sintaks PHP 8.0+ eksklusif seperti `?->`, `str_contains`, atau `str_starts_with` agar kompatibel dengan shared hosting seperti InfinityFree).

---

## 2. Peta Struktur Folder & File

```
pojok_berkah/
├── index.html                  # Template landing page beranda
├── tentang-kami.html           # Halaman profil resmi & navigasi layanan
├── artikel.php                 # Handler katalog /artikel/ dan detail /artikel/{slug}.html
├── render.php                  # Engine Server-Side Rendering (SSR) publik
├── kebijakan-privasi.html      # Halaman legal
├── syarat-ketentuan.html       # Halaman legal
├── 404.html                    # Halaman error 404 (ErrorDocument di .htaccess)
├── sitemap.php                 # XML sitemap dinamis (beranda, halaman statis, artikel terbit)
├── sitemap.xml                 # Fallback XML sitemap statis
├── admin.php                   # Thin wrapper CMS (memanggil admin/index.php)
├── admin/                      # Backend CMS modular
│   ├── index.php               # Router utama + handler form + 8 tab server-side
│   ├── lib/
│   │   ├── security.php        # CSRF, escapeHtml, validasi password, proteksi session
│   │   ├── normalize.php       # Normalisasi no WhatsApp, URL, generator slug artikel
│   │   ├── storage.php         # Operasi tulis data/storage.php via var_export + LOCK_EX
│   │   └── public_render.php   # Engine injeksi SSR publik & auto-sync JSON-LD FAQPage
│   ├── actions/
│   │   ├── login.php           # Setup admin pertama kali, login, ubah password, logout
│   │   ├── save.php            # Simpan WhatsApp & statistik
│   │   ├── save_service.php    # Simpan nama, URL, deskripsi, tag layanan
│   │   ├── upload.php          # Upload & hapus gambar (logo & karya)
│   │   ├── save_faq.php        # Tambah, perbarui, dan hapus pertanyaan/jawaban FAQ
│   │   ├── save_artikel.php    # Simpan, edit, terbitkan, draft, dan hapus artikel
│   │   └── import_artikel.php  # Parser import artikel (HTML/MD/TXT) di server
│   └── views/
│       ├── setup.php           # Form inisialisasi password admin
│       ├── login-form.php      # Form login admin
│       ├── tab-whatsapp.php    # [LIVE] Tab pengaturan WhatsApp
│       ├── tab-logo.php        # [LIVE] Tab logo brand
│       ├── tab-faq.php         # [LIVE] Tab pertanyaan & jawaban
│       ├── tab-artikel.php     # [LIVE] Tab manajemen & import artikel
│       ├── tab-password.php    # [LIVE] Tab ganti password admin
│       ├── tab-layanan.php     # [ARSIP] Tab deskripsi layanan
│       ├── tab-statistik.php   # [ARSIP] Tab angka statistik
│       └── tab-gambar.php      # [ARSIP] Tab slot gambar karya
├── assets/
│   ├── css/
│   │   ├── admin.css           # Styling antarmuka panel admin
│   │   ├── landing.css         # Styling utama landing page & artikel
│   │   ├── effects.css         # Styling interaksi CSS murni, animasi reveal & sticky WA
│   │   └── legal.css           # Styling halaman kebijakan privasi & syarat ketentuan
│   └── img/
│       ├── og-image.jpg        # Gambar fallback Open Graph & Twitter Cards (1200x630)
│       ├── favicon.ico         # Favicon browser
│       └── logo-512.png        # Aset ikon brand
├── data/
│   ├── storage.example.php     # Template struktur penyimpanan (di-commit ke Git)
│   └── storage.php             # Data live & hash password (DI-IGNORE dari Git, proteksi 404)
├── docs/                       # Dokumentasi resmi sistem
│   ├── INDEX-DOKUMEN.md        # Indeks navigasi seluruh dokumen
│   ├── 01-panduan-sistem.md    # Dokumen ini (arsitektur teknis & spesifikasi)
│   ├── 02-panduan-pengguna-cms.md # Panduan instalasi, operasional CMS & API
│   ├── 03-panduan-kontribusi-style.md # Standar kode, keamanan, alur Git & PR
│   ├── 04-roadmap.md           # Rencana pengembangan Fase 2
│   └── CHANGELOG.md            # Catatan riwayat rilis & pembaruan versi
└── _arsip/                     # Folder arsip dokumen & modul masa lalu
    └── docs_lama/              # Proposal dan log historis yang telah selesai dieksekusi
```

---

## 3. Server-Side Rendering (SSR) & Zero-JavaScript

### Alur Kerja SSR Publik
1. Browser pengguna meminta berkas HTML publik (misal `/index.html`, `/tentang-kami.html`).
2. Aturan rewrite di `.htaccess` mengarahkan permintaan ke `render.php?page={nama_halaman}`.
3. `render.php` memuat template HTML terkait dan membaca konfigurasi aktif dari `data/storage.php`.
4. Mesin `admin/lib/public_render.php` memproses HTML di sisi server:
   - Menyuntikkan nomor WhatsApp dinamis ke tautan `href="https://wa.me/..."`.
   - Mengganti elemen logo dengan gambar logo dari CMS atau teks inisial fallback.
   - Mengganti elemen FAQ dengan daftar FAQ korporat aktif.
   - **Auto-Sync FAQPage JSON-LD:** Otomatis memperbarui skema terstruktur `FAQPage` di tag `<head>` agar selalu sinkron dengan FAQ di body HTML.
   - Menyuntikkan kartu artikel terbaru (status `published`) ke grid artikel beranda.
5. HTML yang sudah lengkap disajikan langsung ke browser dengan header `Content-Type: text/html; charset=UTF-8`.

### Interaktivitas Tanpa JavaScript
- **Mobile Menu Drawer:** Menggunakan CSS checkbox hack murni (`<input type="checkbox" id="nav-toggle" class="nav-check">` dan selektor CSS `.nav-check:checked ~ .nav-menu`).
- **Efek Tampilan & Transisi:** Berada di `assets/css/effects.css` memanfaatkan `@keyframes` CSS murni untuk animasi kemunculan elemen (*reveal animation*), bayangan sticky navigation, dan tombol sticky WhatsApp.
- **Tab Panel CMS Admin:** Menggunakan parameter URL `admin.php?tab={nama_tab}` yang dirender secara server-side oleh PHP, sehingga panel admin dapat dioperasikan secara penuh tanpa JavaScript.

---

## 4. Skema Data (`data/storage.php`)

Format penyimpanan adalah array PHP native yang mengembalikan data konfigurasi:

```php
<?php
defined('POJOK_BERKAH_BOOTSTRAPPED') || exit;

return [
    'wa_number'       => '628159501935',    // [LIVE] Digit nomor WhatsApp tujuan
    'password_hash'   => '$2y$10$...',      // Hash password admin (PASSWORD_DEFAULT)
    'faq'             => [                  // [LIVE] Daftar pertanyaan & jawaban
        [
            'question' => 'Apa itu Pojok Berkah?',
            'answer'   => 'Pojok Berkah adalah ruang usaha dan media informasi...',
        ],
        // ... min 1, maks 20 item
    ],
    'articles'        => [                  // [LIVE] Daftar artikel edukasi & rilis
        [
            'slug'         => 'judul-artikel-anda',
            'title'        => 'Judul Artikel Anda',
            'excerpt'      => 'Ringkasan singkat artikel...',
            'content'      => '<p>Isi artikel lengkap...</p>',
            'status'       => 'published', // 'published' atau 'draft'
            'image'        => 'data:image/jpeg;base64,...', // Cover artikel (inline)
            'image_alt'    => 'Deskripsi gambar cover',
            'created_at'   => 1729180000,
            'updated_at'   => 1729180000,
        ],
    ],
    'images'          => [                  // [LIVE] Logo brand & [ARSIP] Gambar karya
        'logo'    => 'data:image/png;base64,...', // Logo situs
        'tryout'  => 'data:image/...',            // [ARSIP] Gambar slot karya
        'gadai'   => 'data:image/...',
        'kitchen' => 'data:image/...',
        'parkir'  => 'data:image/...',
    ],
    'service_urls'    => [...],             // [ARSIP] URL layanan pendukung
    'service_names'   => [...],             // [ARSIP] Nama layanan
    'service_descriptions' => [...],        // [ARSIP] Deskripsi layanan
    'service_tags'    => [...],             // [ARSIP] Tag kata kunci
    'stats'           => [...],             // [ARSIP] Angka statistik
    'login_attempts'  => [                  // Rate limiter brute-force IP
        'hash_hmac_ip' => [
            'count'        => 0,
            'window_start' => 1729180000,
            'locked_until' => 0,
        ],
    ],
];
```

---

## 5. Standar Keamanan & Proteksi Data

1. **Akses Data Storage:** File `data/storage.php` dilindungi konstanta `POJOK_BERKAH_BOOTSTRAPPED` dan aturan `.htaccess` yang me-rewrite seluruh akses langsung ke direktori `data/` menjadi respons HTTP 404.
2. **Manajemen Session & Autentikasi:**
   - Session dikonfigurasi dengan `use_strict_mode = 1`, `use_only_cookies = 1`, `cookie_httponly = 1`, `cookie_samesite = Strict`, dan flag `Secure` jika diakses via HTTPS.
   - Panggilan `session_regenerate_id(true)` dijalankan saat inisialisasi password, login berhasil, perubahan password, dan logout.
3. **Proteksi CSRF (Cross-Site Request Forgery):**
   - Token acak 32-byte kriptografis dihasilkan per session.
   - Setiap form `POST` diverifikasi menggunakan `hash_equals()`.
4. **Rate Limiting Brute-Force Login:**
   - Dibatasi maksimal 5 kali kegagalan login dalam jendela waktu 900 detik (15 menit).
   - Jika terlampaui, alamat IP terkunci selama 900 detik.
5. **Validasi & Sanitasi:**
   - Upload gambar divalidasi MIME type asli via `getimagesize()` dan ekstensi (hanya JPEG, PNG, WEBP; file SVG ditolak untuk mencegah XSS berbasis XML).
   - Seluruh teks input yang ditampilkan di-escape dengan `escapeHtml()` (`htmlspecialchars` dengan flag `ENT_QUOTES | ENT_SUBSTITUTE`).
6. **HTTP Security Headers:**
   - Admin CMS menyematkan header:
     - `X-Content-Type-Options: nosniff`
     - `X-Frame-Options: DENY`
     - `Referrer-Policy: same-origin`
     - `X-Robots-Tag: noindex, nofollow, noarchive`

---

## 6. Standar SEO & Data Terstruktur (Schema.org)

1. **Meta Robots Global:** Seluruh halaman publik memuat `<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">`.
2. **Social Media Graph:** Setiap halaman dilengkapi Open Graph (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`, `og:locale="id_ID"`, `og:site_name`) dan Twitter Cards (`twitter:card="summary_large_image"`).
3. **Fallback Gambar Cover:** Jika artikel atau halaman tidak memiliki gambar spesifik, sistem otomatis menggunakan `assets/img/og-image.jpg` beresolusi 1200x630 piksel.
4. **Data Terstruktur JSON-LD Lengkap:**
   - **Beranda (`index.html`):** Skema `Organization`, `WebSite`, `FAQPage`, dan `ItemList` (Layanan & Karya).
   - **Tentang Kami (`tentang-kami.html`):** Skema `AboutPage` dan `BreadcrumbList`.
   - **Katalog Artikel (`/artikel/`):** Skema `CollectionPage` dan `BreadcrumbList`.
   - **Detail Artikel (`/artikel/{slug}.html`):** Skema `Article` standar Google Rich Results (`headline`, `image`, `datePublished`, `dateModified`, `author`, `publisher` berserta Logo `ImageObject`) dan `BreadcrumbList`.
   - **Halaman Legal:** Skema `WebPage` dan `BreadcrumbList`.
5. **Peta Situs Dinamis & Statis:**
   - `sitemap.php` (disajikan transparan sebagai `/sitemap.xml` melalui `.htaccess`) menghasilkan XML sitemap dinamis yang otomatis menyertakan semua artikel `published`.
   - File `sitemap.xml` statis disediakan sebagai fallback.
