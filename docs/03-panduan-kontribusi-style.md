# 03 — Panduan Kontribusi & Standar Coding — Pojok Berkah

> Dokumen panduan resmi untuk developer dan kontributor.
> Mengatur alur kerja Git, standar penulisan kode PHP/HTML/CSS, pengujian kompatibilitas, prosedur keamanan, dan checklist Pull Request.

---

## 1. Alur Kerja Git & Kontribusi

1. **Branch Utama:** `main` adalah branch produksi. Semua commit yang masuk ke `main` harus siap rilis dan telah diuji secara lokal.
2. **Branch Fitur / Perbaikan:** Buat branch baru dari `main` dengan format:
   - `fitur/nama-fitur` untuk pengembangan fitur baru.
   - `fix/nama-perbaikan` untuk perbaikan bug.
3. **Aturan File Rahasia:**
   - **JANGAN PERNAH** melakukan commit terhadap: `data/storage.php`, kredensial FTP, atau file `.ftp-deploy-sync-state.json`. Seluruhnya sudah diproteksi dalam `.gitignore`.
4. **Deploy Otomatis via GitHub Actions:**
   - Alur kerja otomatis berada di `.github/workflows/deploy.yml` (FTPS eksplisit ke hosting).
   - Pengaturan Secret: `FTP_HOST`, `FTP_USER`, `FTP_PASS`.
   - Variabel: `POJOK_BERKAH_DEPLOY_ENABLED=true` untuk mengaktifkan sinkronisasi otomatis ke shared hosting.

---

## 2. Standar Penulisan PHP (Kompatibilitas PHP 7.4+ & 8.x)

### 2.1 Deklarasi & Struktur
Setiap file fungsional PHP wajib diawali:
```php
<?php
declare(strict_types=1);
```
- File PHP yang hanya berisi fungsi atau logika backend **tidak boleh menggunakan tag penutup `?>`** guna mencegah spasi/newline kosong terkirim secara tidak sengaja.
- Indentasi menggunakan **2 spasi** (bukan tab).

### 2.2 Penamaan (Naming Conventions)
- **Fungsi:** `camelCase` atau `snake_case` yang konsisten (misal: `escapeHtml()`, `saveSettings()`, `normalizeWhatsAppNumber()`).
- **Variabel:** `camelCase` (misal: `$waNumber`, `$csrfToken`, `$articleSlug`).
- **Konstanta:** `UPPER_SNAKE_CASE` (misal: `POJOK_BERKAH_BOOTSTRAPPED`).

### 2.3 Type Hinting & Nullability
- Wajib mendeklarasikan tipe data parameter dan return type:
```php
function escapeHtml(string $value): string
function isValidAdminPassword(string $password): bool
function normalizeWhatsAppNumber(string $value): ?string
```

### 2.4 Aturan Kompatibilitas Versi PHP (Penting untuk Shared Hosting)
Untuk menjamin kompatibilitas di berbagai konfigurasi shared hosting (termasuk InfinityFree):
- **JANGAN** gunakan operator nullsafe `?->` (memerlukan PHP 8.0+ dan dapat menyebabkan fatal error parser di PHP 7.4). Gunakan pengecekan eksplisit:
  ```php
  // BENAR:
  if ($node !== null && $node->textContent !== '') { ... }
  ```
- **JANGAN** gunakan fungsi `str_starts_with()`, `str_contains()`, atau `str_ends_with()` secara langsung tanpa fallback. Gunakan:
  ```php
  // Awalan string:
  if (strpos($haystack, $needle) === 0) { ... }
  
  // Mengandung substring:
  if (strpos($haystack, $needle) !== false) { ... }
  ```
- **Lint Wajib:** Sebelum commit, jalankan linter sintaks PHP lokal:
  ```powershell
  Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
  ```

---

## 3. Standar Frontend & Zero-JavaScript

### 3.1 Zero-JavaScript Policy
- **Tidak ada kode JavaScript klien** di halaman publik (`index.html`, `tentang-kami.html`, artikel, atau legal).
- Interaktivitas wajib diselesaikan menggunakan **CSS murni**:
  - Menu toggle mobile: menggunakan teknik CSS checkbox hack (`.nav-check:checked ~ .nav-menu`).
  - Animasi & transisi: menggunakan CSS transition dan `@keyframes` di `assets/css/effects.css`.
  - Tab CMS: menggunakan link query string server-side `admin.php?tab=...`.

### 3.2 HTML Semantik & SEO
- Setiap halaman wajib memiliki satu tag `<h1>` yang deskriptif.
- Wajib menyertakan `<meta name="viewport" content="width=device-width, initial-scale=1.0">`.
- Wajib memiliki `<meta name="description" ...>` yang relevan (120–160 karakter).
- Wajib menyertakan Open Graph, Twitter Cards, serta data terstruktur Schema.org (JSON-LD) yang valid.
- Semua gambar (`<img>`) wajib memiliki atribut `alt` yang bermakna dan `loading="lazy"` untuk gambar di bawah lipatan layar (*below-the-fold*).

### 3.3 CSS
- Variabel palet warna didefinisikan di `:root` (misal: `--teal: #0B8277;`, `--coral: #FF6B4A;`, `--navy: #1A2B4C;`).
- Pendekatan desain: Mobile-first dengan breakpoint teratur (`700px`, `460px`, `420px`).
- Hindari penggunaan framework CSS berat seperti Bootstrap atau Tailwind CSS untuk menjaga kecepatan *load time* dan kemudahan kustomisasi.

---

## 4. Standar Keamanan (Security Guidelines)

1. **Escape Output Wajib:** Selalu gunakan `escapeHtml($val)` saat mencetak variabel dinamis ke dalam template HTML untuk mencegah Serangan Cross-Site Scripting (XSS).
2. **Validasi CSRF:** Seluruh request form `POST` ke `admin.php` wajib menyertakan input tersembunyi `csrf_token` dan divalidasi dengan `verifyCsrf()`.
3. **Sanitasi File Upload:**
   - Validasi MIME type berkas biner menggunakan `getimagesize()` atau `finfo`.
   - Hanya izinkan format JPEG, PNG, dan WEBP.
   - Tolak file SVG karena potensi injeksi skrip XML.
4. **Proteksi Direktori:**
   - Direktori `data/` harus selalu terblokir dari akses HTTP langsung melalui aturan `.htaccess`.

---

## 5. Prosedur Pengujian Lokal Sebelum PR

1. **Pengujian Sintaks PHP:**
   Jalankan `php -l` untuk seluruh file PHP di repositori dan pastikan 0 error.
2. **Pengujian Alur CMS:**
   - Uji login benar dan verifikasi proteksi lock brute-force (5x salah).
   - Uji penyimpanan nomor WhatsApp dan verifikasi nomor tampil di tombol publik.
   - Uji manajemen FAQ (tambah, edit pertanyaan/jawaban, dan hapus).
   - Uji manajemen artikel: buat artikel draf (pastikan tidak muncul di publik), lalu terbitkan (pastikan tampil di `/artikel/`, detail, sitemap dinamis, dan beranda).
   - Uji ganti password admin.
3. **Pengujian Tampilan Publik:**
   - Periksa konsistensi menu navigasi mobile (buka dan tutup menu via hamburger toggle).
   - Periksa validasi skema JSON-LD menggunakan Google Rich Results Test atau validator Schema.org.
   - Pastikan console browser bersih dari pesan error JavaScript.

---

## 6. Checklist Pull Request (PR)

Sebelum mengajukan merge ke branch `main`, pastikan:
- [ ] Tidak ada file kredensial atau `data/storage.php` yang ikut ter-commit.
- [ ] Seluruh file PHP lulus uji `php -l` tanpa peringatan/error.
- [ ] Prinsip Zero-JavaScript tetap terjaga (tidak ada skrip eksternal/inline JS baru di publik).
- [ ] Seluruh input pengguna disanitasi dan di-escape sebelum dirender ke HTML.
- [ ] Tautan navigasi dan sitemap diperbarui jika ada penambahan rute/halaman baru.
- [ ] Catatan versi diperbarui di `CHANGELOG.md`.
