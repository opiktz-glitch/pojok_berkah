# Log Progres Landing Korporat — Pojok Berkah

> Terakhir diperbarui: 15 Oktober 2026. Semua perubahan masih lokal, belum push ke git.

## Status dokumen

| Dok | Judul | Status |
|---|---|---|
| 01 | Panduan Pengguna & Instalasi | Selesai |
| 02 | Dokumentasi API | Selesai |
| 03 | Spesifikasi Teknis | Selesai |
| 04 | Panduan Kontribusi | Selesai |
| 05 | Rekomendasi Landing Profesional (Opsi A) | Sudah dieksekusi di `index.html` (lokal) |
| 06 | Rekomendasi Lanjutan Korporat (R1–R6) | Tahap 1–3 selesai (kode); menunggu aset pemilik |

## Riwayat eksekusi

### 1. Opsi A — landing korporat tanpa produk (SELESAI, lokal)
- `index.html`: hapus 3 kartu Tryout/Gadai/Kitchen + stats + host + tags + tombol produk; menu baru `Beranda | Tentang | Keunggulan | FAQ | Kontak`; hero/FAQ/footer/SEO ditulis ulang brand umum; JS `service_*` + CSS `.cards/.card/.chips/.stats` dibersihkan (diganti `.about-short`).
- CMS `admin.php` → thin wrapper, `admin/index.php` router + `admin/lib/`, `admin/actions/`, `admin/views/`. `data/storage.php` TIDAK diubah (field layanan tetap disimpan agar subdomain tidak rusak).
- Belum selaras penuh: `tentang-kami.html`, `404.html`, default FAQ `admin.php` masih menyebut produk.

### 2. Perbaikan navigasi lokal (SELESAI, lokal)
- Masalah: link `/` absolut nyasar ke `http://localhost/dashboard/` di XAMPP + sisa `/#layanan` menunjuk section yang sudah dihapus.
- `tentang-kami.html`: nav disamakan (`index.html#beranda/#keunggulan/#faq/#kontak`), logo + footer jadi relatif, tombol `Jelajahi layanan → /#layanan` diganti `Kenali keunggulan kami → index.html#keunggulan`.
- `index.html`: logo `/` → `index.html#beranda`.
- `404.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html`: semua `href="/"` → `index.html`.
- Verifikasi: nol sisa `/#layanan` dan `href="/"` di semua file.

### 3. Tahap 1 — R1 logo + R2 hero/trust bar (SELESAI SEBAGIAN, lokal)
- R1 kode selesai: tagline `Ruang tumbuh untuk belajar dan usaha` + CSS `.logo-text/.logo-tagline` di `index.html` + `tentang-kami.html`; `alt` logo diisi `Logo Pojok Berkah`; mekanisme tampil/sembunyi logo CMS tidak diubah (otomatis saat upload).
- R1 menunggu pemilik situs: upload file logo PNG transparan 512x512 via `admin.php` > Logo situs. Sampai itu dilakukan, kotak `P` generik masih tampil.
- R2 selesai penuh: `.trustbar` 3 item korporat di bawah CTA hero `index.html` (tanpa produk); CTA tetap 2 tombol.

### 4. Tahap 2 — R3 testimoni + R4 cara kerja (SELESAI, lokal)
- `index.html`: tambah section `#cara-kerja` (3 langkah: Kenali kami → Hubungi admin → Dapatkan arahan, tanpa link produk, nomor otomatis via CSS counter) setelah `#keunggulan`.
- `index.html`: tambah section `#testimoni` (3 kartu statis: Rina/Pengunjung, Dimas/Mitra, Sari/Pembaca — peran umum, tanpa produk) sebelum `#faq`.
- CSS baru: `.section-sub`, `.steps/.step`, `.testis/.testi/.avatar/.who`; responsif `<860px` stack 1 kolom. Tanpa JS tambahan, tanpa angka palsu.

### 5. Tahap 3 — R5 mobile/nav + R6 a11y/perf/SEO (SELESAI, lokal)
- R5 `index.html`: tombol hamburger `.nav-toggle` (tanpa library, `aria-expanded` + auto-tutup saat link diklik) + `id="main-nav"`; header shadow `.bar.scrolled` saat scroll >8px (nonaktif animasi bila `prefers-reduced-motion`); responsif `<860px` tampil logo + WA + hamburger, `<480px` H1 2.2rem; padding section distandarkan 80px desktop / 56px mobile.
- R5 `tentang-kami.html`: hamburger + shadow yang sama (`#main-nav-tentang`); `<560px` tampil logo + halaman aktif + WA + hamburger; H1 HP disamakan 2.2rem.
- R6 a11y: underline link footer saat hover (`index.html` + `tentang-kami.html` + `legal.css` untuk halaman legal).
- R6 performa/SEO: `preload` font + hapus `preconnect images.unsplash.com` yang tak terpakai di `index.html` (landing sudah nol gambar eksternal); JSON-LD `Organization/WebSite/FAQPage` umum dipertahankan, tanpa `ItemList` produk.
- R6 tertunda (butuh aset/foto asli dari pemilik): ganti sisa gambar Unsplash di `tentang-kami.html` (3 foto + 3 service) + `404.html`, dan upload logo asli via CMS.

## Ditunda atas permintaan pemilik (15 Okt 2026)

- Push ke git (semua perubahan di atas masih uncommitted/unpushed).
- Upload file logo asli via CMS (R1 sisa) + foto asli pengganti Unsplash (R6 sisa).
- Penyelarasan sisa konten produk di `tentang-kami.html`, `404.html`, default FAQ `admin.php`.

### 16 Okt 2026 — Peningkatan tampilan landing (poin 1–3)

- **Poin 1 — CSS eksternal**: seluruh blok `<style>` `index.html` (9.540 karakter) dipindahkan verbatim ke `assets/css/landing.css`; `<style>` di `index.html` diganti `<link rel="stylesheet" href="assets/css/landing.css">` + fallback `<noscript><style>.reveal{opacity:1;transform:none}</style></noscript>`.
- **Poin 2 — dark mode**: `@media (prefers-color-scheme:dark)` di `landing.css` (token `--bg/--ink/--muted/--line/--card`, latar `.bg`, header, nav-toggle, aksen teal `#3FD8C6`) + `<meta theme-color>` terpisah light/dark di `index.html`.
- **Poin 3 — reveal-on-scroll**: class `.reveal` pada `.about-short`, 3 `.val`, 3 `.step`, 3 `.testi`, `.faq`, `.contact`; stagger `nth-child` 0.08s/0.16s; IIFE `IntersectionObserver` (threshold .1) di `index.html`, fallback: tanpa IO → tampil semua, reduced-motion → tampil tanpa animasi, no-JS → `<noscript>`.
- Tidak ada perubahan `admin.php`, `data/storage.php`, `.htaccess`, `sitemap.xml`, maupun endpoint `?public=1`.

### 16 Okt 2026 — FAQ accordion (poin 4)

- `assets/css/landing.css`: animasi buka/tutup FAQ via `.faq details::details-content` (height 0 → `auto` dengan `interpolate-size:allow-keywords` + transisi `content-visibility allow-discrete`); rule otomatis gugur di browser tanpa dukungan pseudo-element. Override `transition:none` pada `prefers-reduced-motion`.
- `index.html`: fallback JS Web Animations API — satu delegated listener di container `.faq` (selamat dari `faq.replaceChildren()` CMS), deteksi fitur `CSS.supports("selector(::details-content)")` + `interpolate-size`; jika didukung → jalur CSS murni, jika tidak → animasi fade+slide pada konten `<p>` (buka 240ms, tutup 180ms lalu `open=false`). `preventDefault` pada klik `summary` saat fallback aktif.
- Struktur tidak berubah: class `.faq`, tag `<details>/<summary>/<p>` (statis & buatan CMS), dan selector `a[href*="wa.me/"]` tidak disentuh; tanpa elemen wrapper baru.

### 16 Okt 2026 — Sticky WA mobile (poin 5)

- `index.html`: anchor `<a class="wa-sticky wa-button" href="https://wa.me/...">` di setelah `</footer>` (href mengandung `wa.me` → otomatis di-update JS CMS `a[href*="wa.me/"]`); IIFE baru `IntersectionObserver` pada `#kontak` (threshold .35) menambah class `hide` saat section kontak terlihat supaya tidak dobel dengan tombol WA kontak.
- `landing.css`: sticky hanya `<860px`; `position:fixed` full-width `left/right:16px` + `bottom:16px` dengan fallback `env(safe-area-inset-bottom)` (dua deklarasi bertingkat); `z-index:20` (di atas header 10); `body{padding-bottom:96px}` di mobile agar footer tidak tertutup; transisi masuk/keluar opacity+transform (reduced-motion global otomatis mematikan).
- Desktop (>860px) tidak berubah; `admin.php`, `data/`, `.htaccess` tidak disentuh.

### 16 Okt 2026 — Hero decor geometris (poin 6, varian B)

- `index.html`: `<div class="hero-deco" aria-hidden="true"></div>` sebagai anak pertama `section#beranda` — dekorasi murni CSS, tanpa gambar eksternal (konsisten keputusan R6: landing nol-gambar).
- `landing.css`: `.hero{position:relative}`; `.hero-deco` absolute (left/right −8%, height 420px, `z-index:-1` — di atas layer `.bg`, di bawah semua konten termasuk tombol) berisi 3 radial-gradient dari token warna situs + dua cincin lingkaran via `::before/::after`; override alpha khusus dark mode; `@media (prefers-reduced-motion:no-preference)` mengaktifkan animasi `deco-float` (translateY ±14px, 9s & 11s) — otomatis mati saat reduced-motion; `<860px` → height 300px + opacity .75.
- Heading, tombol, trustbar tetap center seperti semula; sentuhan PHP nol.

### 16 Okt 2026 — Redesain hero Varian A (dua kolom) — MENGGANTIKAN Varian B

> Status: SELESAI. Permintaan: design baru untuk landing **tanpa mengganti atribut SEO**. Varian B (`.hero-deco`) dihapus dan digantikan.

- **Kontrak SEO dipertahankan 100%**: blok `<head>` (title, meta description, canonical, OG/Twitter, JSON-LD ×3, fonts), satu `<h1>`, hierarki `<h2>/<h3>`, ID section (`#beranda/#keunggulan/#cara-kerja/#testimoni/#faq/#kontak`), isi FAQ `<details>/<summary>`, link footer — tidak disentuh sama sekali.
- `index.html`: hero kini `.hero-grid` 2 kolom — `.hero-copy` (H1, sub, CTA, trustbar — teks identik semula, rata kiri) + `.hero-visual aria-hidden="true"` berisi panel mockup CSS (`.hp-bar` titik jendela, `.hp-card` avatar+baris teks, 3 `.hp-chip` label melayang di tepi panel, `.hp-graph` 5 bar naik, `.hp-status` titik hijau "Admin online") + 2 `.hero-blob` blur. Div `.hero-deco` Varian B dihapus. `.about-short reveal` tetap di bawah grid.
- `landing.css`: blok hero lama (center) → `.hero-grid{grid-template-columns:1.05fr .95fr;gap:56px}`; panel styling + animasi `hp-grow` (bar tumbuh bersirkat), `hp-pulse` (titik status), `deco-float` (blob) — semua di bawah guard `prefers-reduced-motion:no-preference`; `.title::after` aksen bar gradien; hover `.val/.step/.testi` (translateY + shadow + border biru); override dark mode khusus komponen panel/chip/blob; `<860px>` → 1 kolom, chip dikurangi negatifnya.
- Tidak ada gambar eksternal (konsisten R6); sentuhan `admin.php`/`data/` nol.

### 16 Okt 2026 — Palet warna baru (pro/modern/dinamis) — SEO TIDAK diubah

> Permintaan: kombinasi warna lebih pro, modern, dinamis, eyecatch; **atribut SEO tidak boleh diubah**. Eksekusi 100% di `landing.css` — `index.html` (termasuk seluruh blok `<head>`) tidak disentuh sama sekali.

- **Palet baru (light)**: `--bg:#F7F9FD` `--ink:#0E1526` `--muted:#566783` `--teal:#0E7490` (cyan-700, lolos 4.5:1 utk teks kecil) `--blue:#4F46E5` (indigo) `--amber:#D97706` + token baru `--violet:#7C3AED` & `--cyan:#06B6D4`; `--wa` hijau WhatsApp tidak diubah.
- **Gradient tricolor** cyan→indigo→violet di `.btn-main` (semua stop ≥4.5 vs teks putih), `.grad` H1 (`#0891B2→indigo→amber`, lolos 3:1 utk display), `.title::after`; grafik panel hero `violet→cyan`; glow latar `cyan/indigo/amber`; blob `#818CF8/#22D3EE`.
- **Chip hero**: light `#0E7490/#4F46E5/#B45309` (≥4.5:1), dark `#67E8F9/#A5B4FC/#FCD34D`.
- **Dark mode**: token slate `#0B1220/#E8ECF7/#97A5C0`, gradient bright `#22D3EE/#818CF8/#FBBF24`, aksen `#67E8F9` (trustbar, step, FAQ), glow cyan/indigo/amber.
- Aksen lama (`rgba(11,130,119,…)`, `rgba(47,91,234,…)`, hex `#0B8277/#2F5BEA/#C97400`) diganti di titik yang membawa warna; bayangan netral rgba(16,32,58,…) dibiarkan (visual identik dengan ink baru).

### 16 Okt 2026 — Section layanan dev + karya (4 kartu,含 Parkir) + alur + slot admin Parkir

> Permintaan: Pojok Berkah juga melayani pembuatan aplikasi desktop & web; 3 produk (tryout/gadai/kitchen) = contoh karya; tambah Aplikasi Parkir; gambar diedit via admin. **Atribut SEO `<head>` tetap 100% tidak diubah.** Membalik sebagian Opsi A (produk kembali tampil, tapi framing = portofolio, bukan direktori).

- **`index.html`**: menu `Layanan` (→ `#layanan`); 3 section baru sebelum `#cara-kerja`:
  - `#layanan` — kartu Aplikasi Web / Aplikasi Desktop / Integrasi & Otomasi (ikon SVG inline, tag teknologi PHP/MySQL/JS, Windows/Offline/Laporan, Midtrans/API/WhatsApp) + CTA WhatsApp.
  - `#karya` — 4 kartu `<article class="work">`: Tryout/Gadai/Kitchen (`data-service-*` dari CMS) + **Aplikasi Parkir** (link WA; belum ada subdomain); gambar `data-image-slot` (monogram T/G/K/P sampai admin unggah — 3 bawaan masih Unsplash sampai diganti).
  - `#alur` — 4 langkah Brief→Desain→Bangun→Serah terima (counter `01–04` bawaan `.steps`).
  - JS fetch diperluas: loop generik `img[data-image-slot]` (sekaligus memperbaiki logo header yang tak pernah di-set) + override `service_urls/names/descriptions` untuk 4 slot (guard non-kosong).
- **`admin.php`**: slot `parkir` ditambah ke `$imageSlots`, `$defaultImageUrls` (default `''` → monogram), `$defaultServiceNames` (`Aplikasi Parkir`), `$defaultServiceDescriptions`, `$defaultServiceTags` — loop upload/validasi/form nama+deskripsi+tag otomatis ikut; **`$defaultServiceUrls` TIDAK diisi** (validasi URL baris-316 menolak string kosong → field URL parkir sengaja tidak dibuat; link kartu = WhatsApp). Badge/copy panel: "Arsip…tidak tampil di landing" → "Dipakai Karya, Tentang Kami & 404".
- **`landing.css`**: `.val-ico/.tags/.card-link/.section-cta`, grid `.works` 4→2→1 kolom, monogram `.work-img` gradien cyan→indigo, hover kartu, stagger `nth-child(4)`, override dark mode.
- Kontrak lama utuh: ID section lama, H1, `<details>` FAQ, `wa.me`, `admin.php?public=1`; `?public=1` kini memuat kunci `parkir`.

### 16 Okt 2026 — Modularisasi CMS admin + perbaikan bug session

- **`admin.php`** → thin wrapper 4 baris (`require admin/index.php`).
- **`admin/index.php`** → router utama + semua handler POST + HTML output dengan layout tab (6 tab: WhatsApp, Password, Layanan, Statistik, Gambar, FAQ).
- **`admin/lib/`** → `security.php` (CSRF, escapeHtml, isValidAdminPassword, verifyCsrf), `normalize.php` (normalizeWhatsAppNumber, normalizeServiceUrl), `storage.php` (saveSettings).
- **`admin/actions/`** → `login.php` (setup/login/change_password/logout), `save.php` (WhatsApp/stats), `save_service.php` (nama/URL/deskripsi/tag), `upload.php` (upload/remove image), `save_faq.php`.
- **`admin/views/`** → setup, login-form, tab-whatsapp, tab-password, tab-layanan, tab-statistik, tab-gambar, tab-faq.
- **BUG FIX:** `session_start()` dipindah ke `admin/index.php` sebelum cek `$_SESSION` — sebelumnya `$isAuthenticated` selalu `false` karena `$_SESSION` diakses sebelum `session_start()`.
- **BUG FIX:** `handleChangePassword()` menggunakan parameter `$storagePath` bukan path hardcoded.
- **BUG FIX:** `$imageSlots` di `upload.php` ditambahkan `global` di `handleUploadImage()` dan `handleRemoveImage()` agar tidak `Undefined variable` + `TypeError array_key_exists()`.
- **BUG FIX:** `$defaultServiceNames`, `$defaultServiceUrls`, `$defaultServiceDescriptions`, `$defaultServiceTags` di `save_service.php` ditambahkan `global` di semua 4 function agar tidak `Undefined variable` + `foreach()` null argument.
- **CSS** diekstrak ke `assets/css/admin.css` (90 baris), tidak lagi inline di `admin.php`.
- **16 Okt 2026 (live):** path CSS `/pojok_berkah/assets/css/admin.css` → `assets/css/admin.css` + 3 icon absolut → relatif. Penyebab tampilan polos di live: path absolut hanya valid di localhost subfolder.
- **16 Okt 2026:** encoding 4 file dinormalisasi UTF-16 → UTF-8 tanpa BOM (sebelumnya terdeteksi binary oleh git).
- Tab archive (Password, Layanan, Statistik, Gambar arsip) diberi class `tab-archive` di `admin/index.php` + CSS di `admin.css` agar terlihat beda dari tab utama (WhatsApp, Logo, FAQ).
- Duplikasi `<script>` tab switching dan duplikasi `<section id="tab-password">` di `admin/index.php` dibersihkan — satu script tunggal, satu panel per tab.
- **17 Okt 2026 (live): BUG FIX tab macet** — hanya tab WhatsApp yang bisa dibuka, klik Logo/FAQ/Password/Layanan/Statistik/Gambar arsip hanya ganti hash tapi panel tetap WhatsApp. Penyebab: `str_starts_with()` (PHP 8+) di `admin/lib/normalize.php`, `admin/views/tab-logo.php`, `admin/views/tab-gambar.php` fatal di PHP hosting yang lebih lama → render berhenti setelah tab WhatsApp, `<script>` tab tidak terkirim. Perbaikan: ganti ke `strpos(...) === 0`; JS tab jadi `activate(target, pushHash)` + deep-link hash saat load + `history.replaceState`. Render 7 view lokal OK, `php -l` 12 file lolos.
- **17 Okt 2026 (anti-blokir): tab admin tanpa-JS** — tab aktif dirender server via `?tab=` (`$activeTab`, default `whatsapp`); link `admin.php?tab=...`; POST pertahankan tab via peta `action`→tab (upload `logo`→Logo, slot lain→Gambar); `<script>` tab hanya enhancement (`data-tab`, `replaceState admin.php?tab=`); tambah FAQ tanpa JS via `?faq_extra=N` (maks 20, hanya GET) + link "Tambah baris (tanpa JS)"; hapus tanpa JS = kosongkan lalu Simpan. Verifikasi render `?tab=gambar` 6/6 OK, `php -l` lolos, scan `str_*` PHP 8 bersih.
- **18 Okt 2026 (live): BUG FIX panel Statistik & Gambar kosong** — HTML live terpotong setelah tab Layanan (`<input name="service_descriptions[gadai]` tanpa penutup). Penyebab: data lama di `data/storage.php` live tidak lengkap (bukan JS, bukan DB kosong). Perbaikan: `$serviceNames/Urls/Descriptions/Tags` + `$stats` di-`array_merge` default + tersimpan; `escapeHtml()` terima non-string via cast; hapus canonical admin (`noindex`). Panel Statistik & Gambar kini selalu ter-render walau data lama tidak lengkap.
- PHP syntax check: semua file lulus tanpa error.
- SEO: `admin.php` dan `admin/index.php` tetap `noindex,nofollow,noarchive`; `robots.txt` `Disallow: /admin.php` utuh; `sitemap.xml` tidak berubah.
