# Style Coding — Pojok Berkah

Dokumen ini menguraikan konvensi dan standar pengkodean yang diterapkan di seluruh proyek Pojok Berkah. Tujuannya adalah menjaga konsistensi, keamanan, dan kemudahan pemeliharaan saat banyak orang contribute.

---

## 1. PHP

### 1.1 Deklarasi File
Setiap file PHP harus diawali dengan:

```php
<?php
declare(strict_types=1);
```

Hindari spasi atau output sebelum `<?php`. File PHP yang berisi class/fungsi murni (tidak langsung menghasilkan output) **tidak perlu tag penutup `?>`** untuk mencegah whitespace yang tidak disengaja.

### 1.2 Nama Fungsi & Variabel
- Gunakan **`snake_case`** untuk nama fungsi: `normalizeWhatsAppNumber()`, `saveSettings()`, `escapeHtml()`.
- Gunakan **`camelCase`** untuk variabel lokal: `$waNumber`, `$serviceNames`, `$csrf`, `$flash`.
- Gunakan **`PascalCase`** untuk nama class (jika ada di masa depan).
- Hindari nama singkat yang ambigu (misal `$x`, `$data`). Gunakan nama deskriptif: `$imageSlot`, `$storagePath`, `$submittedCsrf`.

### 1.3 Indentasi & Spasi
- Gunakan **2 spasi** per level indentasi. Jangan gunakan tab.
- Kurung kurawal buka `{` berada di baris yang sama dengan deklarasi:

```php
function normalizeWhatsAppNumber(string $value): ?string
{
  $digits = preg_replace('/\D+/', '', trim($value));
  if ($digits === null || $digits === '') {
    return null;
  }
  return $digits;
}
```

- Spasi setelah koma dalam array dan pemanggilan fungsi:
  ```php
  $settings = ['wa_number' => '6287724039666', 'password_hash' => ''];
  saveSettings($storagePath, $settings);
  ```

### 1.4 Tipe Data & Return
- Deklarasikan tipe parameter dan return type untuk semua fungsi publik:

```php
function escapeHtml(string $value): string
function isValidAdminPassword(string $password): bool
function normalizeServiceUrl(string $value): ?string
```

- Gunakan `?string`, `?int`, dll. untuk nullable.
- Hindari `mixed` jika bisa.

### 1.4b Kompatibilitas Versi PHP (Wajib — Pelajaran 17 Okt 2026)
- Hosting production bisa memakai PHP lebih lama dari lokal (XAMPP 8.2). Semua kode `admin/` **wajib kompatibel PHP 7.4+**:
  - Jangan pakai `str_starts_with()` / `str_contains()` / `str_ends_with()` (baru ada di PHP 8.0). Ganti dengan `strpos($s, $prefix) === 0`.
  - Jangan pakai `match()`, arrow function `fn()`, nullsafe `?->`, `readonly`, `enum`.
- Sebelum push, cari sisa fungsi PHP 8: `Select-String -Pattern 'str_starts_with|str_contains|str_ends_with' admin/`.
- Verifikasi render semua 7 view tab via skrip CLI sebelum push (pernah: render berhenti setelah tab WhatsApp sehingga `<script>` tab tidak terkirim).

### 1.5 Keamanan (Wajib)
Seluruh input dari `$_POST`, `$_GET`, `$_FILES` harus difilter dan divalidasi:

```php
// CSRF
$submittedCsrf = $_POST['csrf'] ?? '';
if (!verifyCsrf($submittedCsrf, $csrf)) {
    http_response_code(403);
    exit;
}

// Escape output
<?= escapeHtml($waNumber) ?>

// Validasi nomor WhatsApp
$waNumber = normalizeWhatsAppNumber($_POST['wa_number'] ?? '');
if ($waNumber === null) {
    $flash = 'Nomor WhatsApp tidak valid.';
    return $settings;
}

// Validasi file upload
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes, true)) {
    $flash = 'Format gambar tidak didukung.';
    return $settings;
}
```

- Semua output ke HTML harus melalui `escapeHtml()`.
- Semua POST harus dicek CSRF melalui `verifyCsrf()`.
- Password harus melalui `password_hash()` / `password_verify()`.
- Simpan setting melalui `saveSettings()` yang menggunakan `LOCK_EX`.

### 1.6 Session & Autentikasi
- Panggil `session_start()` **satu kali di awal** `admin/index.php`, sebelum akses `$_SESSION`.
- Jangan panggil `session_start()` berulang kali di dalam action files.
- Gunakan `session_regenerate_id(true)` saat login dan logout.

---

## 2. HTML

### 2.1 Struktur Dasar
- Gunakan `<!DOCTYPE html>` dan `<html lang="id">`.
- Setiap halaman publik harus memiliki:
  - `<meta charset="utf-8">`
  - `<meta name="viewport" content="width=device-width, initial-scale=1">`
  - `<title>` yang deskriptif
  - `<link rel="canonical">`
  - `<meta name="robots">` (index untuk publik, noindex untuk admin)

### 2.2 Elemen Semantik
Gunakan elemen HTML5 semantik:

```html
<header>...</header>
<nav aria-label="Menu utama">...</nav>
<main class="wrap">...</main>
<section aria-labelledby="...">...</section>
<article>...</article>
<footer>...</footer>
```

### 2.3 Aksesibilitas (ARIA)
- Setiap `<nav>` harus memiliki `aria-label`.
- Elemen interaktif custom harus memiliki `aria-expanded`, `aria-controls`, `aria-current`.
- Gunakan `<label>` untuk setiap input form.
- Gambar informative harus memiliki `alt` yang deskriptif; gambar hias gunakan `aria-hidden="true"`.

### 2.4 Form
- Setiap form POST ke `admin.php` harus menyertakan input CSRF tersembunyi:

```html
<input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
<input type="hidden" name="action" value="save">
```

- Gunakan atribut `required`, `minlength`, `maxlength`, `pattern` untuk validasi klien (selain validasi server).
- Setiap `<input>` dan `<textarea>` harus memiliki pasangan `<label>` dengan `for` yang cocok dengan `id`.

---

## 3. CSS

### 3.1 Variabel (Custom Properties)
Definisikan semua warna, font, dan ukuran di `:root` di awal file CSS:

```css
:root{
  --bg:#F4F8FC;
  --ink:#10203A;
  --muted:#51607A;
  --line:rgba(16,32,58,.12);
  --teal:#0B8277;
  --blue:#2F5BEA;
  --green:#25D366;
  --display:"Sora","Trebuchet MS",system-ui,sans-serif;
  --body:"Inter",system-ui,-apple-system,"Segoe UI",sans-serif
}
```

Gunakan variabel di seluruh stylesheet, hindari hardcode warna.

### 3.2 Organisasi File
- CSS admin tersimpan di `assets/css/admin.css` (diekstrak dari inline di `admin.php`).
- CSS halaman publik tetap inline di masing-masing file HTML (karena struktur landing yang sederhana).
- Setiap file CSS tidak boleh melebihi ~1000 baris; jika lebih, pecah berdasarkan modul.

### 3.3 Media Queries (Mobile-First)
Breakpoints standar proyek:

```css
@media(max-width:700px){ ... }   /* tablet/ponsel */
@media(max-width:460px){ ... }   /* ponsel kecil */
@media(max-width:420px){ ... }   /* ponsel sangat kecil */
```

Tulis style desktop di awal, lalu override di bawahnya dengan media query.

### 3.4 Penamaan Class
- Gunakan **kebab-case**: `.tab-link`, `.tab-active`, `.service-content-group`, `.image-card`.
- Prefix status: `.badge-live`, `.badge-archive`, `.tab-active`, `.tab-panel--active`.
- Hindari nama generik tanpa konteks (misal `.box`, `.div`, `.content`). Gunakan `.panel`, `.preview`, `.logo-card`, `.image-grid`.

---

## 4. JavaScript

### 4.1 Pola Dasar
- Tanpa library/framework. Vanilla JS saja.
- Gunakan IIFE untuk kode yang tidak perlu diekspor:

```javascript
(function(){
  var links = document.querySelectorAll('.tab-link');
  // ...
})();
```

- Deklarasikan variabel dengan `var` (untuk kompatibilitas browser lama di shared hosting) atau `const`/`let` jika target modern.
- JS tab admin hanya enhancement (17 Okt 2026): server sudah merender tab aktif via `?tab=`, JS hanya `activate(target, syncUrl)` + `data-tab` + `preventDefault` + `history.replaceState('admin.php?tab=...')`. Jangan tulis handler tab yang wajib JS (tanpa fallback link `?tab=`).

### 4.2 Event Handling
- Gunakan `addEventListener`, hindari atribut inline `onclick`.
- Hindari duplikasi kode JS di berbagai halaman. Jika fungsi sama (misal `nav-toggle`, FAQ add/remove), ekstrak ke file JS shared.

### 4.3 Fetch API Publik
Semua halaman publik membaca data CMS via:

```javascript
fetch("admin.php?public=1",{cache:"no-store"})
  .then(function(response){return response.json()})
  .then(function(settings){ /* ... */ })
  .catch(function(){});
```

- Selalu sertakan `.catch()` untuk mencegah error konsol jika admin tidak tersedia.
- Gunakan guard `typeof settings.images === "object"` sebelum mengakses properti.

---

## 5. Organisasi File & Folder

```
pojok_berkah/
├── admin.php                  # thin wrapper (require admin/index.php)
├── admin/
│   ├── index.php              # router utama + handler + HTML output
│   ├── lib/
│   │   ├── security.php       # CSRF, escapeHtml, isValidAdminPassword, verifyCsrf
│   │   ├── normalize.php      # normalizeWhatsAppNumber, normalizeServiceUrl
│   │   └── storage.php        # saveSettings
│   ├── actions/
│   │   ├── login.php          # setup, login, change_password, logout
│   │   ├── save.php           # save WhatsApp, save_stats
│   │   ├── save_service.php   # save_service_names/urls/descriptions/tags
│   │   ├── upload.php         # upload_image, remove_image
│   │   └── save_faq.php       # save_faq
│   └── views/
│       ├── setup.php
│       ├── login-form.php
│       ├── tab-whatsapp.php
│       ├── tab-logo.php
│       ├── tab-password.php
│       ├── tab-layanan.php
│       ├── tab-statistik.php
│       ├── tab-gambar.php
│       └── tab-faq.php
├── assets/
│   ├── css/
│   │   ├── admin.css          # diekstrak dari inline
│   │   ├── landing.css        # beranda index.html
│   │   └── legal.css          # halaman legal
│   ├── icons/
│   └── img/
├── data/
│   ├── storage.php            # live settings (git-ignored)
│   ├── storage.example.php    # template
│   └── uploads/               # gambar CMS (git-ignored)
├── docs/                      # dokumentasi proyek
├── _arsip/                    # file tak terpakai
├── .htaccess
├── robots.txt
└── sitemap.xml
```

### Aturan Penempatan File Baru
| Jenis | Lokasi | Contoh |
|-------|--------|--------|
| CSS admin | `assets/css/` | `assets/css/admin.css` |
| CSS halaman publik | Inline di file HTML | — |
| Fungsi PHP utilitas | `admin/lib/` | `admin/lib/security.php` |
| Handler POST CMS | `admin/actions/` | `admin/actions/save.php` |
| Tampilan HTML admin | `admin/views/` | `admin/views/tab-whatsapp.php` |
| Halaman publik baru | root | `kontak.html` |
| File tak terpakai | `_arsip/` | — |

---

## 6. Keamanan

1. **CSRF**: Setiap form POST harus menyertakan token CSRF dan divalidasi dengan `hash_equals()`.
2. **Escape Output**: Semua variabel yang ditampilkan di HTML melalui `escapeHtml()`.
3. **Password**: Simpan sebagai hash dengan `password_hash(PASSWORD_DEFAULT)`, verifikasi dengan `password_verify()`.
4. **File Upload**: Validasi MIME type (`image/jpeg`, `image/png`, `image/webp`), ukuran maksimal 5 MB, simpan sebagai base64 di `storage.php`.
5. **Session**: `session_start()` sekali di awal, `session_regenerate_id(true)` saat login/logout.
6. **Admin Terproteksi**: `admin.php` dan `admin/index.php` memiliki `<meta robots content="noindex,nofollow,noarchive">` dan diblokir di `robots.txt`.
7. **Storage Terproteksi**: `data/storage.php` diblokir via `.htaccess` (`RewriteRule ^data/ - [R=404,L]`).

---

## 7. SEO (Halaman Publik)

Setiap halaman publik harus memiliki:

| Elemen | Contoh |
|--------|--------|
| `<title>` | `<title>Tentang Kami | Pojok Berkah</title>` |
| `<meta name="description">` | Maks 160 karakter, deskriptif |
| `<link rel="canonical">` | URL lengkap `https://pojokberkah.online/tentang-kami.html` |
| Open Graph | `og:title`, `og:description`, `og:url`, `og:image`, `og:locale`, `og:site_name` |
| Twitter Card | `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image` |
| JSON-LD | `Organization`, `WebSite`, `FAQPage` (jika relevan) |
| Favicon | `favicon-32x32.png`, `favicon.ico`, `apple-touch-icon.png` |

**Tidak boleh** menambahkan halaman admin ke `sitemap.xml`. Admin harus selalu `noindex`.

---

## 8. Dokumentasi

Setiap perubahan struktur atau fitur harus diikuti pembaruan dokumen:

| Dokumen | Isi |
|---------|-----|
| `docs/01-panduan-pengguna.md` | Cara instalasi dan penggunaan CMS |
| `docs/02-dokumentasi-api.md` | Endpoint `?public=1`, `?image=`, semua action POST |
| `docs/03-spesifikasi-teknis.md` | Arsitektur, skema storage, keamanan, SEO |
| `docs/04-panduan-kontribusi.md` | Git flow, konvensi kode, testing, deploy |
| `docs/08-struktur-folder.md` | Peta folder dan aturan penempatan file |
| `docs/07-log-progres.md` | Catatan riwayat perubahan |
| `docs/09-rekomendasi-rapikan-admin.md` | Rekomendasi UI admin |

---

## 9. Checklist Sebelum Commit/Push

- [ ] `php -l` lolos untuk semua file PHP yang diubah
- [ ] Tidak ada secret atau `data/storage.php` yang ikut commit
- [ ] Semua output HTML melalui `escapeHtml()`
- [ ] Semua form POST memiliki input CSRF
- [ ] Validasi server ada untuk setiap input pengguna
- [ ] Tag SEO halaman publik tidak berusang (title, description, canonical, robots)
- [ ] `admin.php` dan `admin/index.php` tetap `noindex,nofollow,noarchive`
- [ ] Dokumen yang relevan diperbarui (03, 04, 08, 09, 07)

---

**Versi dokumen:** 1.0 — 16 Oktober 2026
**Dikelola oleh:** Tim pengembang Pojok Berkah