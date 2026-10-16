# 11 — Rekomendasi Fase 2 — Pojok Berkah

Dokumen ini mengkumpulkan semua rekomendasi yang belum dilakukan untuk Fase 2, berdasarkan `docs/09-rekomendasi-rapikan-admin.md`, `docs/06-rekomendasi-lanjutan-korporat.md`, dan `docs/07-log-progres.md`.

---

## Status Singkat

| Tahap | Jumlah | Prioritas | Estimasi |
|-------|--------|-----------|----------|
| 2A — Admin Minimal (Paket A) | 3 | Tinggi | 2,5 jam |
| 2B — Admin Lanjutan (Paket B) | 4 | Sedang | 4–5 jam |
| 2C — Landing Korporat | 2 | Sedang | 3–5 jam |
| 2D — Opsional (DITOLAK) | 1 | Rendah | — |
| **Total** | **10** | | **10–15 jam** |

---

## Tahap 2A — Rekomendasi Admin Minimal (Paket A)

Wajib diselesaikan terlebih dahulu. Risiko nyaris nol.

### R0 — Selaraskan Default FAQ (Wajib)

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R0

**Masalah:** `$defaultFaq` di `admin/index.php` masih 4 item produk. Saat storage kosong, API mengembalikan FAQ produk dan JS mengganti FAQ korporat.

**Aksi:**
- [ ] Ganti `$defaultFaq` di `admin/index.php` dengan 4 item sama persis dengan fallback `index.html`:
  1. Apa itu Pojok Berkah?
  2. Bagaimana cara menghubungi admin?
  3. Apakah saya perlu membuat akun untuk melihat informasi?
  4. Di mana saya bisa membaca informasi resmi?
- [ ] Pastikan storage kosong menghasilkan `?public=1` dengan FAQ korporat

**Risiko:** Nyaris nol  
**Estimasi:** 30 menit

---

### R4 — Perbaiki Copy Basi

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R4

**Masalah:** Beberapa copy di admin masih menyebut fitur yang sudah dihapus dari landing (kartu beranda, 3 layanan, dll.).

**Aksi:**
- [ ] Perbaiki lead di `admin/index.php` yang menyebut "nama, alamat, deskripsi, gambar, statistik" (tidak ada field alamat)
- [ ] Perbaiki copy di views yang menyebut "tampil di kartu, footer, Tentang Kami" (kartu beranda sudah dihapus)
- [ ] Pastikan semua copy sesuai dengan branding korporat (bukan nama produk)
- [ ] Periksa semua `views/tab-*.php` untuk copy yang mengacu pada halaman beranda

**Risiko:** Nyaris nol  
**Estimasi:** 1 jam

---

### R5 — Badge Status Per Panel

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R5

**Masalah:** Tidak ada indikator visual mana panel yang dipakai landing dan mana yang arsip.

**Aksi:**
- [ ] Tambah `.badge-live` (hijau, "Dipakai landing") untuk panel: WhatsApp, Logo, FAQ
- [ ] Tambah `.badge-archive` (abu, "Arsip — tidak tampil di landing") untuk panel: Layanan, Statistik, Gambar
- [ ] Tambah CSS sekitar 10 baris di `assets/css/admin.css`

**Risiko:** Kecil  
**Estimasi:** 1 jam

---

## Tahap 2B — Rekomendasi Admin Lanjutan (Paket B)

Berdasarkan paket B dari `docs/09`. Handler POST tidak diubah, hanya susunan tampilan.

### R1 — Susun Ulang Urutan Panel

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R1

**Masalah:** Urutan panel saat ini tidak optimal; panel yang paling sering digunakan (WA, Logo, FAQ) tidak di atas.

**Aksi:**
- [x] Urutan baru: 1) Nomor WA, 2) Logo situs, 3) FAQ, 4) sisanya masuk `<details>` arsip
- [x] Pindahkan panel FAQ + WA + Logo ke paling atas
- [x] Sesuaikan `admin/index.php` dan views terkait

**Risiko:** Kecil  
**Estimasi:** 1–2 jam

---

### R2 — Bungkus Panel Yatim dalam `<details>` Arsip

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R2

**Masalah:** Panel "Konten layanan" dan "Statistik beranda" tidak dirender landing tetapi tampil paling menonjol di admin.

**Aksi:**
- [x] Bungkus "Konten layanan" (`admin/views/tab-layanan.php`) dalam `<details>` berlabel "Arsip — tidak tampil di landing, tetap tersimpan untuk Tentang Kami/subdomain"
- [x] Bungkus "Statistik beranda" (`admin/views/tab-statistik.php`) dalam `<details>` berlabel yang sama
- [x] Handler POST (`save_service_*`, `save_stats`) TIDAK diubah, tetap jalan normal

**Risiko:** Kecil  
**Estimasi:** 1 jam

---

### R3 — Pisahkan Logo vs Gambar Arsip

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R3

**Masalah:** Logo dan gambar layanan dicampur dalam satu grid, membuat tampilan admin berantakan.

**Aksi:**
- [x] Slot `logo` jadi kartu tunggal paling atas (file baru `admin/views/tab-logo.php`)
- [x] 3 slot lain (tryout/gadai/kitchen) tetap di grid tapi dalam `<details>` "Gambar arsip (dipakai Tentang Kami dan 404)"
- [x] Sesuaikan `admin/views/tab-gambar.php` (Logo dihapus, sisakan hanya Gambar layanan)

**Risiko:** Kecil  
**Estimasi:** 1 jam

---

### R6 — Tandai Dokumen API Aktif vs Arsip

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R6

**Masalah:** Dokumentasi API tidak membedakan kunci yang masih dipakai landing dan yang sudah arsip.

**Aksi:**
- [ ] Update `docs/02-dokumentasi-api.md`: tandai kunci aktif vs arsip di tabel response
- [ ] Update `docs/03-spesifikasi-teknis.md`: jelaskan alur baca baru hanya `wa_number`, `images.logo`, `faq`
- [ ] Pastikan tabel di dokumentasi menandai mana yang "LIVE" dan mana yang "ARSIP"

**Risiko:** Kecil  
**Estimasi:** 1 jam

---

## Tahap 2C — Sisa Rekomendasi Landing Korporat

Berdasarkan `docs/06-rekomendasi-lanjutan-korporat.md` (R1–R6), tahap 1–3 sudah selesai kode, menunggu aset pemilik.

### R5 — Header & Navigasi Mobile Profesional ✅ SELESAI

**Referensi:** `docs/06-rekomendasi-lanjutan-korporat.md` §R5

**Masalah:** Di ≤860px semua link disembunyikan, hanya sisa tombol WA. Belum ada hamburger yang lengkap, sticky CTA, maupun shadow saat scroll di semua halaman.

**Aksi (selesai):**
- Hamburger menu di `tentang-kami.html` sudah ada, disesuaikan konsisten dengan `index.html` (toggle + aria-expanded/aria-label)
- Sticky bottom CTA WhatsApp khusus mobile ditambahkan ke `tentang-kami.html` (satu tombol, tidak bersaing dengan tombol header); `index.html` sudah memiliki `wa-sticky`
- Shadow header saat scroll di semua halaman publik (`index.html`, `tentang-kami.html`) via `.bar.scrolled` + JS scroll listener
- Semua menu bisa diakses di HP 360px tanpa berdesakan (CSS mobile ≤560px/≤860px, `flex:1 1 100%` pada link menu)
- `prefers-reduced-motion` sudah dihormati (transisi dimatikan)

**Catatan:** Sticky WA di `tentang-kami.html` disembunyikan otomatis saat section `.contact` terlihat (IntersectionObserver threshold .35), mencegah duplikasi dengan tombol WA kontak.

**Risiko:** Sedang  
**Estimasi:** 2–3 jam

---

### R6 — Aksesibilitas, Performa & SEO ✅ SELESAI

**Referensi:** `docs/06-rekomendasi-lanjutan-korporat.md` §R6

**Masalah:** Beberapa quick win a11y dan performa belum selesai di semua halaman.

**Aksi (selesai):**
- Underline link footer saat hover di semua halaman publik (`index.html`, `tentang-kami.html` via CSS; `kebijakan-privasi.html`, `syarat-ketentuan.html` sudah ada di `legal.css`)
- Preload font ditambahkan ke `tentang-kami.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html` (`index.html` sudah ada); preconnect `images.unsplash.com` yang tidak terpakai dihapus dari `tentang-kami.html`
- JSON-LD Organization + WebSite ditambahkan ke `tentang-kami.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html` (`index.html` sudah lengkap dengan Organization/WebSite/FAQPage)
- Contrast ratio dicek: teks primer `#10203A` dan muted `#51607A` pada background `#F4F8FC` memenuhi kontras AAA (≥7:1); tidak perlu perubahan warna

**Risiko:** Kecil  
**Estimasi:** 1–2 jam

---

## Tahap 2D — Opsional / Masa Depan (Prioritas Rendah — JANGAN Dilakukan Sekarang)

Berdasarkan paket C dan R7 dari `docs/09`.

### R7 — Opsional (DITOLAK)

**Referensi:** `docs/09-rekomendasi-rapikan-admin.md` §R7

**Masalah:** Ada saran untuk menghapus total `service_*/stats` dari backend.

**Keputusan:** **DITOLAK** untuk saat ini.  
**Alasan:** `tentang-kami.html` dan `404.html` masih memakai `service_*` (nama, URL, deskripsi, tag, gambar). Menghapus backend legacy akan merusak halaman tersebut.

**Aksi:**
- [ ] Tidak ada aksi — ditunda hingga ada kebutuhan clara dari pemilik situs
- [ ] CMS untuk testimoni/cara-kerja juga ditunda

**Risiko:** TINGGI  
**Estimasi:** —

---

## Prioritas Eksekusi (Urutan Disarankan)

| Urutan | Rekomendasi | Tahap | Dokumen Referensi | Estimasi |
|--------|-------------|-------|-------------------|----------|
| 1 | R0 — Selaraskan default FAQ | 2A | `docs/09` §R0 | 30 menit |
| 2 | R4 — Perbaiki copy basi | 2A | `docs/09` §R4 | 1 jam |
| 3 | R5 — Badge status per panel | 2A | `docs/09` §R5 | 1 jam |
| 4 | ~~R1 — Susun ulang urutan panel~~ | ~~2B~~ | ~~`docs/09` §R1~~ | ~~1–2 jam~~ |
|   | ✅ **SELESAI** |   |   |   |
| 5 | ~~R2 — Bungkus panel yatim arsip~~ | ~~2B~~ | ~~`docs/09` §R2~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** |   |   |   |
| 6 | ~~R3 — Pisahkan logo vs gambar arsip~~ | ~~2B~~ | ~~`docs/09` §R3~~ | ~~1 jam~~ |
|   | ✅ **SELESAI** |   |   |   |
| 7 | R6 — Tandai dokumen API | 2B | `docs/09` §R6 | 1 jam |
| 8 | R5 — Header & navigasi mobile | 2C | `docs/06` §R5 | 2–3 jam |
| 9 | R6 — Aksesibilitas, performa, SEO | 2C | `docs/06` §R6 | 1–2 jam |

**Total estimasi: 10–15 jam**

---

## Kriteria Selesai Fase 2

- [ ] Storage kosong menghasilkan `?public=1` dengan FAQ korporat (R0)
- [ ] Sisa kata Tryout/Gadai/Kitchen di admin hanya dalam blok arsip berlabel (R2, R3)
- [ ] Semua POST lama tetap 200; `index.html` tanpa error console (R0–R6)
- [ ] `admin.php` dan `admin/index.php` tetap `noindex,nofollow,noarchive` (SEO aman)
- [ ] Semua dokumen terkait diperbarui (02, 03, 07, 09)
- [ ] CHANGELOG diperbarui dengan rilis Fase 2

---

## Catatan Penting

1. **Jangan** melakukan Paket C (hapus backend legacy) — akan merusak `tentang-kami.html` dan `404.html` yang masih memakai `service_*`.
2. Semua perubahan harus diuji dengan `php -l` sebelum commit.
3. Backup produksi manual sebelum deploy besar.
4. Jalankan workflow `dry_run=true` sebelum push ke produksi.
5. Dokumen style coding (`docs/10-style-coding.md`) harus diikuti untuk semua perubahan kode.

---

**Dikelola oleh:** Tim pengembang Pojok Berkah  
**Tanggal dibuat:** 16 Oktober 2026  
**Status:** Draft — siap untuk dieksekusi