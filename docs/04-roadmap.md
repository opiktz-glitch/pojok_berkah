# 04 — Roadmap Pengembangan — Pojok Berkah

> Dokumen rencana pengembangan aplikasi Pojok Berkah.
> Memuat evaluasi penyelesaian Fase 2 dan daftar usulan fitur untuk Fase 3 mendatang.

---

## 1. Status Evaluasi Fase 2 (Oktober 2026) — 100% SELESAI ✅

Seluruh perbaikan arsitektur dan modernisasi CMS pada Fase 2 telah selesai diimplementasikan ke dalam kode aktif:

| Paket / Fitur | Status | Realisasi Kode & Hasil |
|---|---|---|
| **R0 — Sinkronisasi Default FAQ** | ✅ SELESAI | `$defaultFaq` di CMS diselaraskan dengan 4 FAQ korporat beranda. |
| **R1 — Urutan Panel CMS** | ✅ SELESAI | Urutan tab admin ditata: WhatsApp → Logo → FAQ → Artikel → Password → Arsip. |
| **R2 & R3 — Pemisahan Tab Arsip** | ✅ SELESAI | Tab `layanan`, `statistik`, dan `gambar` dilabeli badge arsip abu-abu. |
| **R4 — Pembersihan Copy Basi** | ✅ SELESAI | Seluruh copy lama mengacu ke landing korporat dan arsitektur Zero-JS. |
| **R5 — Badge Status Visual** | ✅ SELESAI | Implementasi `.badge-live` hijau dan `.badge-archive` abu di panel admin. |
| **R6 — Navigasi Mobile Zero-JS** | ✅ SELESAI | Drawer navigasi mobile berbasis CSS checkbox hack murni tanpa JS. |
| **R7 — Audit & Optimasi SEO** | ✅ SELESAI | Skema Schema.org (Article, BreadcrumbList, FAQPage, Organization), dynamic sitemap, dan Open Graph. |
| **R8 — Modular CMS Artikel** | ✅ SELESAI | Penambahan tab CMS artikel, upload cover, draf/terbit, dan parser impor server. |

---

## 2. Rencana Pengembangan Fase 3 (Mendatang)

Daftar pertimbangan fitur untuk iterasi berikutnya:

### 3.1 Manajemen Konten Artikel Lanjutan
- **Kategori / Tag Artikel:** Menambahkan filter kategori (Edukasi, Berita, Pengumuman) pada halaman katalog `/artikel/`.
- **Pagination Katalog:** Jika jumlah artikel mencapai >20 artikel terbit, tambahkan paginasi server-side (`/artikel/?p=2`).
- **Estimasi Waktu Baca:** Menghitung perkiraan waktu baca artikel secara otomatis di server (misal: "3 menit membaca").

### 3.2 Analitik Sederhana & Privacy-Friendly
- **Zero-Cookie Analytics:** Menghitung jumlah tayangan halaman publik tanpa menggunakan cookie pelacak pihak ketiga atau JavaScript klien (misal: pencatatan hit sederhana di server dengan hashing IP).

### 3.3 Pencadangan & Pemulihan Otomatis
- **Tombol Backup Data CMS:** Menambahkan aksi di panel admin untuk mengunduh arsip zip atau salinan `data/storage.php` langsung dari dashboard.

---

## 3. Catatan Arsitektur Jangka Panjang

1. **Mempertahankan Zero-JS di Sisi Klien:** Website publik tetap mengutamakan kecepatan muat instan tanpa JavaScript klien.
2. **Tanpa Ketergantungan Database Eksternal:** Selama volume konten dalam skala portofolio/portal UMKM, arsitektur flat-file PHP native tetap dipertahankan karena keandalan tinggi dan biaya hosting minimal.
