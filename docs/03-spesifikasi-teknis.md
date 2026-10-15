# Spesifikasi Teknis — Pojok Berkah

## 1. Arsitektur

- Tipe: static landing (HTML+CSS+JS inline) + 1 file backend `admin.php` (PHP 8.0+, tanpa framework, tanpa database, tanpa composer).
- Alur baca: browser `fetch admin.php?public=1` → PHP baca `data/storage.php` → JSON → JS menimpa `wa.me`, `[data-service-link/name/description]`, `.chips`, `[data-stat-value/label]`, `img[data-image-slot]`, `.faq`.
- Catatan pasca-korporat (16 Okt 2026): `index.html` hanya membaca `wa_number`, `images.logo`, `faq`. Sisa kunci (`service_*`, `stats`, 3 gambar) adalah ARSIP untuk `tentang-kami.html` + `404.html`, bukan landing.
- Alur tulis: form POST ke `admin.php` → validasi → `saveSettings()` tulis `data/storage.php` via `var_export` + `LOCK_EX`.
- Proteksi server: `.htaccess` (HTTPS redirect kecuali localhost, www→non-www, blok `^data/` → 404, sembunyikan `.ftp-deploy-sync-state.json`, `ErrorDocument 404 /404.html`).

## 2. Struktur file

```
index.html, tentang-kami.html, kebijakan-privasi.html, syarat-ketentuan.html, 404.html
admin.php                  # CMS + API publik + serve gambar
data/storage.example.php   # template (boleh di-Git)
data/storage.php           # live settings + password_hash (JANGAN di-Git)
data/uploads/*.jpg|png|webp# upload CMS (JANGAN di-Git)
.htaccess, robots.txt, sitemap.xml, legal.css
.github/workflows/deploy.yml
```

## 3. Skema data (data/storage.php)

File me-return array PHP. Kunci yang dipakai:

```php
[
  'wa_number' => '628159501935',      // string digit
  'password_hash' => '$2y$10$...',    // password_hash(PASSWORD_DEFAULT)
  'service_urls' => ['tryout'=>..., 'gadai'=>..., 'kitchen'=>...],
  'service_names' => [...], 'service_descriptions' => [...],
  'service_tags' => ['tryout'=>[...max 8], ...],
  'stats' => ['services'=>['value','label'], 'gadai'=>[...], 'kitchen'=>[...]],
  'faq' => [['question','answer'], ...max 20],
  'images' => ['logo'=>'<32hex>.png', ...],  // nama file di data/uploads/
  'login_attempts' => [ hash_hmac_ip => ['count','window_start','locked_until'] ],
]
```

Default fallback didefinisikan di `admin.php` (`$defaultFaq`, `$defaultServiceUrls`, `$defaultStats`, `$defaultImageUrls` Unsplash) bila kunci kosong.

Upload: nama acak `bin2hex(random_bytes(16)).ext`, file lama di-`unlink` bila tak dipakai slot lain.

## 4. Keamanan

- Password: `password_hash`/`password_verify`, min 6 char + huruf & angka, max 72.
- Session: `use_strict_mode`, `use_only_cookies`, `httponly`, `Secure` (jika HTTPS), `SameSite=Strict`, `regenerate_id` saat login/logout/ganti password.
- CSRF: token 32-byte per session, `hash_equals` di semua POST.
- Rate-limit login: 5 gagal / 900 detik per hash HMAC IP → lock 900 detik.
- Upload: `getimagesize` + whitelist IMAGETYPE_JPEG/PNG/WEBP, tolak SVG.
- Header CMS: `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: same-origin`, `X-Robots-Tag: noindex,nofollow,noarchive`.
- `storage.php` punya guard `POJOK_BERKAH_BOOTSTRAPPED` + aturan `.htaccess` + `Disallow: /data/, /admin.php` di robots.

## 5. SEO & performa

- Canonical, OG/Twitter, JSON-LD Organization/WebSite/FAQPage, sitemap.xml, favicon set, Google Fonts + Unsplash via preconnect.
- Tidak ada build step; batas hosting: file HTML/PHP ≤1MB, lainnya ≤10MB.

## 6. Lingkungan

- Lokal: XAMPP, HTTP boleh. Produksi: InfinityFree PHP 8.3, wajib HTTPS + `data/` writable (755/644, fallback 777/666).
