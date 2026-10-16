# Roadmap Fase 2 — Pojok Berkah

Dokumen ini merencanakan tahapan pengembangan untuk **Fase 2** setelah penyelesaian modularisasi CMS admin (Fase 1). Diradarkan dari rekomendasi dalam `docs/09-rekomendasi-rapikan-admin.md`, `docs/06-rekomendasi-lanjutan-korporat.md`, dan `docs/07-log-progres.md`.

---

## Status Saat Ini (Fase 1 — SELESAI)

| Item | Status | Tanggal |
|------|--------|---------|
| Modularisasi `admin.php` → folder `admin/` (lib, actions, views) | ✅ Selesai | 16 Okt 2026 |
| Ekstrak CSS admin ke `assets/css/admin.css` | ✅ Selesai | 16 Okt 2026 |
| Konsistenkan indentasi (2 spasi) + `.vscode/settings.json` | ✅ Selesai | 16 Okt 2026 |
| Perbaikan navigasi `tentang-kami.html` (tambah link Layanan) | ✅ Selesai | 16 Okt 2026 |
| Perbaikan bug `session_start()` + path storage hardcoded | ✅ Selesai | 16 Okt 2026 |
| Dokumen style coding (`docs/10-style-coding.md`) | ✅ Selesai | 16 Okt 2026 |
| Changelog (`docs/CHANGELOG.md`) | ✅ Selesai | 16 Okt 2026 |
| Verifikasi SEO aman (admin noindex, robots.txt, sitemap) | ✅ Selesai | 16 Okt 2026 |

---

## Fase 2 — Rencana Pelaksanaan

Fase 2 difokuskan pada **penyelesaian rekomendasi UI admin yang tertunda** dan **penyelesaian sisa rekomendasi landing korporat** yang belum selesai.

### 🗓️ Tahap 2A — Rekomendasi Admin (Prioritas Tinggi)

Berdasarkan `docs/09-rekomendasi-rapikan-admin.md`, paket A (minimal aman) harus diselesaikan terlebih dahulu.

#### R0 — Selaraskan Default FAQ (Wajib) ✅ SELESAI
- Ganti `$defaultFaq` di `admin/index.php` dengan 4 item yang sama persis dengan fallback `index.html`:
  1. Apa itu Pojok Berkah?
  2. Bagaimana cara menghubungi admin?
  3. Apakah saya perlu membuat akun untuk melihat informasi?
  4. Di mana saya bisa membaca informasi resmi?
- Pastikan storage kosong menghasilkan `?public=1` dengan FAQ korporat
- **Risiko:** Nyaris nol
- **Estimasi:** 30 menit
- **Catatan:** Sudah terpenuhi — `$faqItems` di `admin/index.php` (baris 102-107) sudah sama persis dengan JSON-LD FAQ di `index.html` (baris 34). **Audit 16 Okt 2026: endpoint `?public=1` sempat TIDAK mengirim `faq` (hanya `wa_number`, `service_*`, `images`, `stats`) sehingga edit FAQ di CMS tak tampil di landing — diperbaiki dengan menambah `'faq' => $faqItems` + guard `is_array`/cast agar data lama tak fatal. Terverifikasi: `?public=1` kini kirim 4 FAQ korporat.**

#### R4 — Perbaiki Copy Basi ✅ SELESAI
- Perbaiki lead di `admin/index.php`: "tampil di" → "digunakan di" (baris 139)
- Tinjauan semua `views/tab-*.php`: tidak ada copy yang merujuk ke "kartu", "alamat field", atau "3 layanan"
- Copy sudah sesuai branding korporat
- **Risiko:** Nyaris nol
- **Estimasi:** 1 jam (selesai lebih cepat karena copy sudah bersih saat modularisasi)

#### R6 — Tandai Dokumen API Aktif vs Arsip ✅ SELESAI
- `docs/02-dokumentasi-api.md`: setiap kolom respons JSON ditandai `[LIVE]` / `[ARSIP]`; tabel action ditambahkan kolom "Status data" dengan `[LIVE]` untuk `wa_number`, `faq`; `[ARSIP]` untuk `service_*`, `stats`, dan gambar non-logo
- `docs/03-spesifikasi-teknis.md`: skema data `storage.php` ditandai `[LIVE]` / `[ARSIP]` per kunci; catatan pasca-korporat diperbarui
- **Risiko:** Nyaris nol (hanya dokumentasi)
- **Estimasi:** 1 jam (selesai)

#### R5 — Badge Status Per Panel ✅ SELESAI
- [x] Tambah `.badge-live` (hijau, "Dipakai landing") untuk panel WhatsApp, Logo, FAQ — terverifikasi di `admin/views/tab-whatsapp.php`, `tab-logo.php`, `tab-faq.php`
- [x] Tambah `.badge-archive` (abu, "Arsip — tidak tampil di landing") untuk panel Layanan, Statistik, Gambar — terverifikasi di `admin/views/tab-layanan.php`, `tab-statistik.php`, `tab-gambar.php`
- [x] CSS di `assets/css/admin.css` (baris 44-46: `.badge`, `.badge-live`, `.badge-archive`)
- **Risiko:** Kecil
- **Estimasi:** 1 jam (selesai — kode sudah ada; 16 Okt 2026 hanya verifikasi docs, tanpa ubah SEO)

---

### 🗓️ Tahap 2B — Rekomendasi Admin Lanjutan (Prioritas Sedang)

Berdasarkan paket B dari `docs/09`.

#### R1 — Susun Ulang Urutan Panel ✅ SELESAI (implementasi: tab, bukan details)
- [x] Urutan baru: 1) Nomor WA, 2) Logo situs, 3) FAQ, 4) Password, Layanan, Statistik, Gambar arsip sebagai tab arsip (`tab-archive`)
- [x] Panel FAQ + WA + Logo naik ke paling atas (link `admin.php?tab=whatsapp/logo/faq`)
- **Risiko:** Kecil
- **Estimasi:** 1–2 jam
- **Catatan audit 16 Okt 2026:** docs lama menyebut `<details>` lipat — implementasi aktual memakai tab tanpa-JS via `?tab=` + class `tab-archive` (lihat `admin/index.php` baris 241-278). Hasil sama: panel utama di depan, arsip dibedakan visual.

#### R2 — Bungkus Panel Yatim dalam Arsip ✅ SELESAI (implementasi: tab arsip)
- [x] "Konten layanan" sebagai tab arsip berlabel "Arsip — tidak tampil di landing" (`admin/views/tab-layanan.php` baris 2, `badge-archive`)
- [x] "Statistik beranda" sebagai tab arsip berlabel yang sama (`admin/views/tab-statistik.php` baris 2)
- [x] Handler POST (`save_service_*`, `save_stats`) TIDAK diubah, tetap jalan
- **Risiko:** Kecil
- **Estimasi:** 1 jam
- **Catatan audit 16 Okt 2026:** docs lama menyebut `<details>` lipat — implementasi aktual memakai tab + `tab-archive` (link `admin.php?tab=layanan/statistik`). Label arsip ada di badge + tab, bukan `<details>`.

#### R3 — Pisahkan Logo vs Gambar Arsip ✅ SELESAI
- [x] Slot `logo` jadi kartu tunggal paling atas (file `admin/views/tab-logo.php`)
- [x] 4 slot lain (tryout/gadai/kitchen/parkir) tetap di grid dalam tab "Gambar arsip (dipakai Karya, Tentang Kami dan 404)"
- **Risiko:** Kecil
- **Estimasi:** 1 jam
- **Catatan audit 16 Okt 2026:** docs lama menyebut 3 slot — aktual 4 slot non-logo karena slot `parkir` ditambah (lihat `$imageSlots` di `admin/index.php` baris 108-114). Label arsip ada di badge + tab, bukan `<details>`.

#### R6 — Tandai Dokumen API Aktif vs Arsip ✅ SELESAI
- [x] Update `docs/02-dokumentasi-api.md`: tandai kunci aktif vs arsip
- [x] Update `docs/03-spesifikasi-teknis.md`: alur baca baru hanya `wa_number`, `images.logo`, `faq`
- **Risiko:** Kecil
- **Estimasi:** 1 jam

---

### 🗓️ Tahap 2C — Sisa Rekomendasi Landing Korporat (Prioritas Sedang)

Berdasarkan `docs/06-rekomendasi-lanjutan-korporat.md` (R1–R6) yang statusnya "Tahap 1–3 selesai kode; menunggu aset pemilik".

#### R5 — Header & Navigasi Mobile Profesional ✅ SELESAI
- Hamburger menu di `tentang-kami.html` sudah ada, disesuaikan konsisten dengan `index.html` (toggle + aria-expanded/aria-label)
- Sticky bottom CTA WhatsApp khusus mobile ditambahkan ke `tentang-kami.html` (satu tombol, tidak bersaing dengan tombol header); `index.html` sudah memiliki `wa-sticky`
- Shadow header saat scroll di semua halaman publik (`index.html`, `tentang-kami.html`) via `.bar.scrolled` + JS scroll listener
- Semua menu bisa diakses di HP 360px tanpa berdesakan (CSS mobile ≤560px/≤860px, `flex:1 1 100%` pada link menu)
- `prefers-reduced-motion` sudah dihormati (transisi dimatikan)
- **Catatan:** Sticky WA di `tentang-kami.html` disembunyikan otomatis saat section `.contact` terlihat (IntersectionObserver threshold .35), mencegah duplikasi dengan tombol WA kontak
- **Risiko:** Sedang
- **Estimasi:** 2–3 jam

#### R6 — Aksesibilitas, Performa & SEO ✅ SELESAI
- Underline link footer saat hover di semua halaman publik (`index.html`, `tentang-kami.html` via CSS; `kebijakan-privasi.html`, `syarat-ketentuan.html` sudah ada di `legal.css`)
- Preload font ditambahkan ke `tentang-kami.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html` (index.html sudah ada); preconnect `images.unsplash.com` yang tidak terpakai dihapus dari `tentang-kami.html`
- JSON-LD Organization + WebSite ditambahkan ke `tentang-kami.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html` (index.html sudah lengkap dengan Organization/WebSite/FAQPage)
- Contrast ratio dicek: teks primer `#10203A` dan muted `#51607A` pada background `#F4F8FC` memenuhi kontras AAA (≥7:1); tidak perlu perubahan warna
- **Risiko:** Kecil
- **Estimasi:** 1–2 jam

---

### 🗓️ Tahap 2D — Opsional / Masa Depan (Prioritas Rendah)

Berdasarkan paket C dan R7 dari `docs/09`.

#### R7 — Opsional (JANGAN Sekarang)
- [ ] Hapus total `service_*/stats` dari backend — **DITOLAK** karena `tentang-kami.html` masih butuh
- [ ] CMS untuk testimoni/cara-kerja — ditunda
- **Risiko:** TINGGI — merusak `tentang-kami.html` / `404.html`

---

## Prioritas Eksekusi (Urutan Disarankan)

| Urutan | Tindakan | Dokumen Referensi | Estimasi |
|--------|----------|-------------------|----------|
| 1 | ~~R0 — Selaraskan default FAQ~~ | ~~`docs/09` §R0~~ | ~~30 menit~~ |
|   | ✅ **SELESAI** — Sudah sama persis dengan `index.html` JSON-LD |   |   |
| 2 | ~~R4 — Perbaiki copy basi~~ | ~~`docs/09` §R4~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** — Copy sudah bersih saat modularisasi, hanya lead text diperbaiki |   |   |
| 3 | ~~R5 — Badge status per panel~~ | ~~`docs/09` §R5~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** — `.badge-live`/`.badge-archive` sudah di semua `tab-*.php` + CSS `admin.css` |   |   |
| 4 | ~~R1 — Susun ulang urutan panel~~ | ~~`docs/09` §R1~~ | ~~1–2 jam~~ |
|   | ✅ **SELESAI** — WA → Logo → FAQ di atas, sisanya sebagai tab arsip (`tab-archive`) |   |   |
| 5 | ~~R2 — Bungkus panel yatim arsip~~ | ~~`docs/09` §R2~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** — Layanan & Statistik sebagai tab arsip berlabel + badge arsip |   |   |
| 6 | ~~R3 — Pisahkan logo vs gambar arsip~~ | ~~`docs/09` §R3~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** — Logo di `tab-logo.php` terpisah, 4 slot gambar arsip di tab Gambar |   |   |
| 7 | ~~R6 — Tandai dokumen API~~ | ~~`docs/09` §R6~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** — `docs/02` dan `docs/03` ditandai [LIVE]/[ARSIP] per kunci/action |   |   |
| 8 | R5 Mobile — header & navigasi | `docs/06` §R5 | 2–3 jam |
| 9 | R6 — Aksesibilitas, performa, SEO | `docs/06` §R6 | 1–2 jam |

**Total estimasi:** 10–15 jam

---

## Kriteria Selesai Fase 2

- [x] Storage kosong menghasilkan `?public=1` dengan FAQ korporat (R0) ✅ SELESAI
- [ ] Sisa kata Tryout/Gadai/Kitchen di admin hanya dalam blok arsip berlabel (R2, R3)
- [ ] Semua POST lama tetap 200; `index.html` tanpa error console (R0–R6)
- [ ] `admin.php` dan `admin/index.php` tetap `noindex,nofollow,noarchive` (SEO aman)
- [ ] Semua dokumen terkait diperbarui (02, 03, 07, 09)
- [ ] CHANGELOG diperbarui dengan rilis Fase 2

---

## Catatan

- **Jangan** melakukan Paket C (hapus backend legacy) karena akan merusak `tentang-kami.html` dan `404.html` yang masih memakai `service_*`.
- Semua perubahan harus diuji dengan `php -l` sebelum commit.
- Backup produksi manual sebelum deploy besar.
- Jalankan workflow `dry_run=true` sebelum push ke produksi.

---

**Dikelola oleh:** Tim pengembang Pojok Berkah  
**Tanggal dibuat:** 16 Oktober 2026  
**Status:** Draft — siap untuk dieksekusi