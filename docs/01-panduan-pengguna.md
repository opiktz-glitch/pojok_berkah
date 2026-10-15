# Panduan Pengguna & Instalasi — Pojok Berkah

> Berlaku untuk situs `pojokberkah.online` (landing + CMS PHP tanpa database).
> Terakhir diperbarui: 15 Oktober 2026.

## 1. Gambaran aplikasi

Pojok Berkah adalah **landing site + CMS mini** untuk direktori 3 layanan:

| Slot | Nama default | URL default |
|------|--------------|-------------|
| `tryout` | Tryout TKA | https://tryout.pojokberkah.online |
| `gadai` | Gadai BPKB | https://gadai.pojokberkah.online |
| `kitchen` | Alfi Kitchen | https://kitchen.pojokberkah.online |

Halaman publik: `index.html`, `tentang-kami.html`, `kebijakan-privasi.html`, `syarat-ketentuan.html`, `404.html`.
Halaman admin: `admin.php` (satu file: frontend CMS + backend + API publik).

Semua konten dinamis (nomor WA, nama/URL/deskripsi/tag layanan, statistik, FAQ, gambar) disimpan di `data/storage.php` dan dibaca halaman publik via `fetch("admin.php?public=1")`.

## 2. Kebutuhan sistem

- PHP 8.0+ (produksi InfinityFree memakai PHP 8.3)
- Apache dengan `mod_rewrite` (untuk `.htaccess`)
- Folder `data/` writable oleh PHP
- HTTPS aktif di produksi

## 3. Instalasi lokal (XAMPP)

1. Clone/copy proyek ke `c:\xampp\htdocs\pojok_berkah`.
2. Copy contoh penyimpanan:
   ```
   copy data\storage.example.php data\storage.php
   ```
3. Jalankan Apache di XAMPP. Jangan buka file dengan `file://`.
4. Buka `http://localhost/pojok_berkah/admin.php` → buat password admin (min. 6 karakter, ada huruf + angka).
5. Buka `http://localhost/pojok_berkah/index.html` untuk melihat situs.
6. Di CMS: isi nomor WhatsApp (format `628...`), URL layanan, FAQ, upload gambar (JPG/PNG/WebP, maks 5 MB).

## 4. Deploy produksi (InfinityFree)

1. Tambahkan domain `pojokberkah.online` ke hosting, arahkan nameserver ke `ns1/ns2.infinityfree.com` atau verifikasi CNAME.
2. Minta sertifikat SSL gratis, tunggu hingga `https://` bisa diakses (wajib sebelum upload `.htaccess`).
3. Upload isi proyek ke `htdocs/` via File Manager / FTPS (`ftpupload.net:21`) atau GitHub Actions (lihat `CONTRIBUTING`).
4. Buat `data/storage.php` produksi: setting di localhost (nomor WA produksi + password), lalu upload file tersebut **manual/privat**. Jangan commit ke Git (sudah di `.gitignore`).
5. Set permission: `data/` → 755 (fallback 777), `data/storage.php` → 644 (fallback 666).
6. Verifikasi: `/admin.php` bisa dibuka via HTTPS, akses langsung `/data/...` balas 404, homepage menampilkan nomor WA dari CMS.

## 5. Cara pakai CMS (admin)

1. Login di `/admin.php` dengan password. Gagal 5x dalam 15 menit → dikunci 15 menit.
2. Panel yang tersedia:
   - Nomor WhatsApp (8–15 digit, `0...` otomatis jadi `62...`)
   - Alamat layanan (URL penuh `http(s)://`, maks 2048 char)
   - Nama, deskripsi, tag (maks 8 tag/layanan), statistik, FAQ (maks 20 item)
   - Gambar: logo + 3 layanan (maks 5 MB, dimensi ≤ 12000 px, otomatis hapus file lama)
   - Ganti password & Logout
3. Semua form memakai token CSRF session. Selalu Logout jika di perangkat bersama.

## 6. Backup & troubleshooting

- Backup: simpan salinan `data/storage.php` + isi `data/uploads/` dari hosting (tidak ada di Git).
- Gagal simpan → cek izin tulis `data/`.
- Deploy klaim "nothing changed" → hapus `.ftp-deploy-sync-state.json` di `htdocs/` lalu deploy ulang.
- Setup admin hanya dari localhost saat belum ada password; di produksi upload `storage.php` yang sudah ada hash-nya.

Kontak: admin@pojokberkah.online
