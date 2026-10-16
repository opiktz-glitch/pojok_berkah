# Changelog — Pojok Berkah

Semua perubahan tercatat dalam file ini. Format berdasarkan [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

## [1.0.6] — 17 Oktober 2026

### 🐛 Perbaikan Tab Admin Macet di Live (PHP 7 di Hosting)

- `admin/lib/normalize.php`: `str_starts_with($digits, '0')` → `strpos($digits, '0') === 0`.
- `admin/views/tab-logo.php`, `admin/views/tab-gambar.php`: `str_starts_with(...)` → `strpos(...) === 0`.
  Penyebab: fungsi `str_*` hanya ada di PHP 8+. PHP di hosting lebih lama sehingga render berhenti (fatal error) tepat setelah tab WhatsApp — panel Logo/FAQ/Password/Layanan/Statistik/Gambar tidak ikut ter-render dan `<script>` tab tidak sampai ke browser. Gejala: CSS tampil, URL hash berubah, tapi panel tetap WhatsApp.
- `admin/index.php`: JS tab ditulis ulang jadi fungsi `activate(target, pushHash)` + deep-link hash saat load (`#tab-gambar` langsung membuka panel Gambar) + `history.replaceState` saat klik. Mencegah tab macet bila hash tidak cocok.
- Verifikasi: `php -l` lolos 12 file admin; render 7 view lokal OK (tab-whatsapp 1271 B, tab-logo 1169 B, tab-faq 1093 B, tab-password 1304 B, tab-layanan 5045 B, tab-statistik 1602 B, tab-gambar 4560 B); `normalizeWhatsAppNumber('081234567890')` → `6281234567890`.

## [1.0.5] — 16 Oktober 2026

### 🐛 Perbaikan Tampilan Admin di Live (CSS 404)

- `admin/index.php`: path CSS `/pojok_berkah/assets/css/admin.css` → `assets/css/admin.css`; 3 icon `/assets/icons/...` → relatif.
  Penyebab: path absolut `/pojok_berkah/...` hanya benar di localhost subfolder, di live (root domain) menjadi 404 sehingga halaman tampil polos tanpa styling.
- Normalisasi encoding UTF-16 → UTF-8 tanpa BOM dan newline di `admin/index.php`, `admin/actions/upload.php`, `admin/actions/save_service.php`, `assets/css/admin.css` (sebelumnya terdeteksi binary oleh git).

## [1.0.4] — 16 Oktober 2026

### 🐛 Perbaikan Scope Variabel & Duplikasi Kode

- `admin/actions/upload.php`: tambah `global $imageSlots` di `handleUploadImage()` dan `handleRemoveImage()` — memperbaiki fatal error `TypeError: array_key_exists()` saat upload/hapus gambar.
- `admin/actions/save_service.php`: tambah `global $defaultServiceNames/Urls/Descriptions/Tags` di semua 4 function — memperbaiki fatal error `foreach() argument must be array` saat simpan nama/URL/deskripsi/tag layanan.
- `admin/index.php`: hapus duplikasi `<script>` tab switching dan duplikasi `<section id="tab-password">`; konsolidasi ke satu script + satu panel per tab; tambah class `tab-archive` pada tab Password/Layanan/Statistik/Gambar arsip.
- `assets/css/admin.css`: tambah aturan CSS untuk `.tab-link.tab-archive` dan `.tab-panel.tab-archive`.

## [1.0.3] — 16 Oktober 2026

### ✅ R6 Selesai — Tandai Dokumen API Aktif vs Arsip
- `docs/02-dokumentasi-api.md`: setiap kolom respons JSON ditandai `[LIVE]` / `[ARSIP]`; tabel action ditambahkan kolom "Status data"
- `docs/03-spesifikasi-teknis.md`: skema data `storage.php` ditandai `[LIVE]` / `[ARSIP]` per kunci; catatan pasca-korporat diperbarui

### 🔍 R6c — SEO: Sitemap & Robots.txt
- `sitemap.xml`: `lastmod` diperbarui ke `2026-10-16` untuk `tentang-kami.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html`
- `robots.txt`: ditambahi `Disallow: /admin/` (selain `/admin.php`, `/data/`, `/_arsip/`)

---

## [1.0.2] — 16 Oktober 2026

### ✅ R4 Selesai — Perbaiki Copy Basi
- Lead di `admin/index.php` (baris 139): "tampil di" → "digunakan di"
- Tinjauan semua `views/tab-*.php`: tidak ada copy yang merujuk ke "kartu", "alamat field", atau "3 layanan"
- Copy sudah sesuai branding korporat

---

## [1.0.1] — 16 Oktober 2026

### ✅ R0 Selesai — Selaraskan Default FAQ
- `$defaultFaq` di `admin/index.php` (baris 81-86) sudah sama persis dengan JSON-LD FAQ di `index.html` (baris 34)
- Storage kosong akan menghasilkan `?public=1` dengan FAQ korporat, bukan FAQ produk

---

## [1.0.0] — 16 Oktober 2026

### 🔧 Peningkatan Struktur Admin (Modularisasi)

File `admin.php` yang sebelumnya monolitik (~1021 baris) telah dibagi menjadi struktur modular di folder `admin/`:

| File Baru | Fungsi |
|-----------|--------|
| `admin/index.php` | Router utama + semua handler POST + HTML output dengan layout tab |
| `admin/lib/security.php` | CSRF token, `escapeHtml()`, `isValidAdminPassword()`, `verifyCsrf()` |
| `admin/lib/normalize.php` | `normalizeWhatsAppNumber()`, `normalizeServiceUrl()` |
| `admin/lib/storage.php` | `saveSettings()` dengan `LOCK_EX` |
| `admin/actions/login.php` | setup, login, change_password, logout |
| `admin/actions/save.php` | save WhatsApp, save statistik |
| `admin/actions/save_service.php` | save nama/URL/deskripsi/tag layanan |
| `admin/actions/upload.php` | upload_image, remove_image |
| `admin/actions/save_faq.php` | save FAQ |
| `admin/views/setup.php` | Tampilan buat akun admin |
| `admin/views/login-form.php` | Tampilan login |
| `admin/views/tab-whatsapp.php` | Tab WhatsApp |
| `admin/views/tab-password.php` | Tab Password |
| `admin/views/tab-layanan.php` | Tab Layanan (nama, URL, deskripsi, tag) |
| `admin/views/tab-statistik.php` | Tab Statistik |
| `admin/views/tab-gambar.php` | Tab Gambar (logo + layanan) |
| `admin/views/tab-faq.php` | Tab FAQ |

File root `admin.php` kini merupakan **thin wrapper** (4 baris) yang hanya melakukan `require admin/index.php`.

### 🎨 Prioritas 1 — Ekstrak CSS

- CSS admin (90 baris) diekstrak dari inline `<style>` di `admin.php` ke file terpisah:
  - `assets/css/admin.css`
- `admin.php` / `admin/index.php` sekarang menggunakan `<link rel="stylesheet" href="assets/css/admin.css">`
- Tidak ada perubahan pada aturan CSS, hanya pemindahan lokasi file

### 📐 Prioritas 2 — Konsistenkan Indentasi

- Seluruh kode PHP di `admin.php` dan file modular baru menggunakan **2 spasi** per level indentasi (tidak ada campuran spasi/tab)
- File `.vscode/settings.json` dibuat untuk menstandarkan editor:
  - `editor.tabSize: 2`
  - `editor.insertSpaces: true`
  - `editor.detectIndentation: false`
  - `files.trimTrailingWhitespace: true`
  - `files.insertFinalNewline: true`

### 🐛 Perbaikan Bug Kritikal

| Bug | Lokasi | Penyebab | Perbaikan |
|-----|--------|----------|-----------|
| `$isAuthenticated` selalu `false` meski login berhasil | `admin/index.php:26` | `$_SESSION` diakses sebelum `session_start()` dipanggil | Ditambahkan `session_start()` di baris 6, sebelum akses `$_SESSION` |
| `handleChangePassword()` menyimpan ke path hardcoded | `admin/actions/login.php:138` | Menggunakan `__DIR__ . '/../../data/storage.php'` bukan parameter `$storagePath` | Diubah ke `saveSettings($storagePath, $settings)` |

### 🎯 Perbaikan Navigasi `tentang-kami.html`

- Menambahkan tautan **Layanan** (`index.html#layanan`) ke menu navigasi
- Urutan menu disamakan dengan `index.html`: Beranda → Tentang → Keunggulan → **Layanan** → FAQ → Kontak → WhatsApp
- Sebelumnya: Layanan hilang dari navigasi, dan urutan Layanan/Keunggulan terbalik

### 📖 Dokumentasi yang Diperbarui

| Dokumen | Perubahan |
|---------|-----------|
| `docs/01-panduan-pengguna.md` | Deskripsi admin: `admin.php` → thin wrapper + `admin/` modular |
| `docs/03-spesifikasi-teknis.md` | Struktur file `admin/` ditambahkan, lokasi default fallback diperbarui |
| `docs/04-panduan-kontribusi.md` | Referensi `admin.php` → `admin/index.php` + `admin/actions/` + `admin/views/` |
| `docs/07-log-progres.md` | Entri progres modularisasi + 2 bug fix ditambahkan |
| `docs/08-struktur-folder.md` | Folder `admin/` dengan subfolder `lib/`, `actions/`, `views/` ditambahkan |
| `docs/09-rekomendasi-rapikan-admin.md` | Referensi baris lama diperbarui, status diubah ke EKSEKUSI |
| `docs/INDEX-DOKUMEN.md` | Entri `10 — Style Coding` ditambahkan |

### 📄 Dokumen Baru

- `docs/10-style-coding.md` — Konvensi lengkap PHP, HTML, CSS, JavaScript, keamanan, SEO, organisasi file, dan checklist commit (266 baris)

### ✅ Verifikasi

- `php -l` lolos untuk **semua 10 file PHP** baru dan dimodifikasi
- SEO aman: `admin.php` dan `admin/index.php` tetap `<meta robots content="noindex,nofollow,noarchive">`
- `robots.txt` tetap `Disallow: /admin.php`
- `sitemap.xml` tidak berubah (hanya halaman publik)
- `.github/workflows/deploy.yml` tidak perlu perubahan (folder `admin/` tidak ada di daftar `exclude`)
- `.gitignore` tidak perlu perubahan (`admin/` tidak di-exclude, kode sumber harus di-commit)

---

## Versi Sebelumnya

Versi sebelum 16 Oktober 2026 tidak tercatat secara terstruktur dalam file ini.

---

**Dikelola oleh:** Tim pengembang Pojok Berkah  
**Terakhir diperbarui:** 16 Oktober 2026