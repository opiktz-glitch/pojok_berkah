# Rekomendasi Lanjutan Landing Korporat — Selaras Opsi A

> Status: REKOMENDASI / RENCANA — belum dieksekusi.
> Dibuat: 15 Oktober 2026. Lanjutan dari `05-rekomendasi-landing-profesional.md` (Opsi A sudah dikerjakan di `index.html`).
> Prinsip: TETAP tanpa Tryout/Gadai/Alfi Kitchen di landing. Semua usulan bersifat korporat/brand umum.
> Acuan audit lama (10 poin, dibuat sebelum Opsi A): hanya diambil yang masih relevan; sisanya dinyatakan kedaluwarsa (lihat bagian 7).

## 1. R1 — Branding: logo asli (Prioritas Tertinggi)

- Masalah: logo masih kotak gradasi huruf `P` + teks; favicon belum konsisten.
- Aksi:
  1. Siapkan logo PNG transparan 512x512 (dan versi 180px untuk apple-touch bila perlu).
  2. Upload via CMS: `admin.php` > Logo situs. Verifikasi tampil di header `index.html`, `tentang-kami.html`, halaman legal, dan favicon ikut berganti (JS CMS sudah menangani `settings.images.logo`).
  3. Tambahkan tagline kecil di bawah/di samping logo header, contoh: `Ruang tumbuh untuk belajar dan usaha` (ukuran ~0.72rem, warna muted).
  4. Samakan `og-image.jpg` (pastikan 1200x630) dengan identitas baru bila diganti.
- Kriteria selesai: tidak ada lagi fallback `P` yang terlihat saat logo CMS terisi; favicon konsisten di semua halaman.

## 2. R2 — Hero: perkuat copy + trust bar korporat

- Kondisi: hero baru sudah korporat (`Pojok Berkah — ruang tumbuh...`), tapi belum menjual bukti.
- Aksi:
  1. Pertahankan H1, poles sub-copy dengan janji yang bisa diverifikasi (contoh arah, bukan klaim final): `Mudah dihubungi lewat WhatsApp dan email • Informasi disajikan jelas • Kebijakan transparan di setiap halaman`.
  2. Tambahkan trust bar tepat di bawah CTA (3 item dengan ikon ✓, tanpa menyebut produk).
  3. CTA tetap 1 primer (`Kenali kami → #keunggulan`) + 1 sekunder WA (`Tanya admin`). Jangan tambah tombol ketiga.
- Kriteria selesai: H1 + sub + trust bar terbaca dalam 5 detik tanpa scroll.

## 3. R3 — Social proof korporat (pengganti stats produk)

- Masalah: section stats produk sudah dihapus (benar); landing kini tanpa angka/bukti sama sekali.
- Aksi:
  1. Tambahkan section `Apa kata mereka` — 3 kartu statis (nama + peran umum, misal: Pengunjung, Mitra, Pembaca). Jangan sebut nama produk. Tandai sebagai testimoni awal bila datanya belum banyak.
  2. Bila belum ada data jujur, JANGAN pasang angka palsu. Alternatif sementara: strip komitmen (`Fast-respon • Jelas • Transparan`) sebagai pengganti angka.
  3. Jika nanti ada data riil (misal tahun berjalan, jumlah artikel, rating), tampilkan sebagai 3 kartu angka + konteks, bukan angka polos.
- Kriteria selesai: ada minimal 1 bukti sosial yang jujur dan bisa diverifikasi.

## 4. R4 — Section Cara Kerja 3 langkah (baru)

- Masalah: alur Beranda → Tentang singkat → Keunggulan → FAQ → Kontak belum memandu pengunjung baru.
- Aksi: sisipkan setelah `#keunggulan`:
  1. `Kenali kami` — baca Tentang & Keunggulan di halaman ini.
  2. `Hubungi admin` — via WhatsApp atau email di `#kontak`.
  3. `Dapatkan arahan` — admin membantu langkah berikutnya.
- Kriteria selesai: 3 langkah tampil dalam 1 baris desktop / stack rapi di mobile, tanpa link produk.

## 5. R5 — Header & navigasi mobile profesional

- Masalah: di ≤860px semua link disembunyikan (`.nav a:not(.btn-wa){display:none}`), hanya sisa tombol WA. Belum ada hamburger, sticky CTA, maupun shadow saat scroll.
- Aksi:
  1. Tambahkan hamburger menu (tanpa library): tombol + panel dropdown berisi 5 link menu. Hormati `prefers-reduced-motion`.
  2. Tambahkan shadow header saat scroll (5 baris JS tambah class).
  3. Pertimbangkan sticky bottom CTA WA khusus mobile (satu tombol, tidak bersaing dengan tombol header).
  4. Standarkan padding section: 80px desktop / 56px mobile; batasi H1 di <480px ke ~2.6rem.
- Kriteria selesai: semua menu bisa diakses di HP 360px tanpa berdesakan; tidak ada layout-shift.

## 6. R6 — Aksesibilitas, performa & SEO (quick win, tetap korporat)

- Aksesibilitas:
  1. Isi `alt` deskriptif untuk logo (`alt="Logo Pojok Berkah"`) dan gambar lain yang tersisa; `alt=""` hanya untuk dekoratif murni.
  2. Underline link footer saat hover; pastikan fokus keyboard terlihat (sudah ada `:focus-visible`, pertahankan).
- Performa:
  1. Tambah `preload` font/hero bila perlu; `fetchpriority="high"` untuk gambar pertama yang terlihat.
  2. Ganti sisa gambar Unsplash eksternal di `tentang-kami.html` + `404.html` dengan aset lokal terkompresi (WebP, `width/height` + `loading="lazy"` kecuali hero).
- SEO:
  1. Pertahankan `Organization` + `WebSite` + `FAQPage` umum. JANGAN tambah `ItemList` 3 produk (kedaluwarsa oleh Opsi A).
  2. Pastikan `sitemap.xml` + canonical + OG konsisten dengan copy korporat baru.

## 7. Status audit lama (10 poin, pra-Opsi A)

| # | Topik lama | Status kini |
|---|---|---|
| 1 | Logo asli | Belum → menjadi R1 di dokumen ini |
| 2 | Copy hero produk + trust bar produk | Kedaluwarsa sebagian → diganti R2 (korporat) |
| 3 | Ganti foto Unsplash 3 kartu produk | Kedaluwarsa di `index.html` (kartu dihapus) → sisa di `tentang-kami.html`/`404.html` masuk R6 |
| 4 | Testimoni produk + stats produk | Kedaluwarsa → diganti R3 (korporat) |
| 5 | Cara kerja berbasis produk | Kedaluwarsa → diganti R4 (korporat) |
| 6 | Kartu layanan setara + disclaimer OJK | Kedaluwarsa (kartu dihapus Opsi A) |
| 7 | Hamburger / sticky WA / shadow | Belum → menjadi R5 di dokumen ini |
| 8 | Tipografi & whitespace | Belum → masuk R5 |
| 9 | Alt, kontras, reduced-motion | Sebagian (reduced-motion OK) → sisa masuk R6 |
| 10 | Preload, ItemList produk | Sebagian (display=swap OK); ItemList produk kedaluwarsa → sisa masuk R6 |

## 8. Urutan eksekusi yang disarankan

- Tahap 1 (~1 jam): R1 logo + R2 hero/trust bar.
- Tahap 2 (~2 jam): R3 testimoni + R4 cara kerja.
- Tahap 3 (lain waktu): R5 mobile/nav + R6 a11y/perf/SEO.
