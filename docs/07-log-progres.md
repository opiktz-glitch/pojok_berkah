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
- CMS `admin.php` + `data/storage.php` TIDAK diubah (field layanan tetap disimpan agar subdomain tidak rusak).
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
