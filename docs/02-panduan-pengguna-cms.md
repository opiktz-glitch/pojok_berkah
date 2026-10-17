# 02 — Panduan Pengguna CMS & Dokumentasi API — Pojok Berkah

> Berlaku untuk instalasi lokal dan produksi `pojokberkah.online`.
> Dokumen ini memuat panduan lengkap instalasi lokal (XAMPP), deployment ke shared hosting, panduan operasional CMS admin (Zero-JS), dan spesifikasi API backend.

---

## 1. Kebutuhan Lingkungan Sistem

- **PHP:** Versi 7.4 hingga 8.2+ (InfinityFree menggunakan PHP 8.2/8.3).
- **Web Server:** Apache dengan modul `mod_rewrite` aktif (wajib untuk `.htaccess`).
- **Izin Berkas:** Direktori `data/` harus memiliki izin tulis (*writeable*) oleh pengguna web server (755 atau 775 di Linux/hosting).
- **Koneksi:** HTTPS wajib aktif di lingkungan produksi.

---

## 2. Panduan Instalasi Lokal (XAMPP / Windows)

1. Letakkan folder proyek di `c:\xampp\htdocs\pojok_berkah`.
2. Siapkan file konfigurasi penyimpanan awal:
   ```powershell
   copy data\storage.example.php data\storage.php
   ```
3. Buka XAMPP Control Panel dan nyalakan modul **Apache**.
4. Buka browser dan akses alamat setup CMS:
   ```
   http://localhost/pojok_berkah/admin.php
   ```
5. Buat kata sandi admin pertama kali (minimal 6 karakter, kombinasi huruf dan angka).
6. Buka beranda website di `http://localhost/pojok_berkah/` untuk memastikan SSR berjalan dengan benar.

---

## 3. Panduan Deployment Produksi (InfinityFree / Shared Hosting)

1. Tambahkan domain `pojokberkah.online` ke akun hosting, arahkan nameserver ke NS hosting atau atur DNS record CNAME/A.
2. Aktifkan sertifikat SSL gratis (Let's Encrypt / ZeroSSL) di cPanel hosting hingga protokol `https://` aktif.
3. Unggah seluruh file proyek ke folder `htdocs/` via File Manager atau FTPS (`ftpupload.net:21`).
4. **Keamanan File Storage:**
   - Siapkan file `data/storage.php` yang sudah dikonfigurasi di lokal.
   - Unggah berkas tersebut secara manual ke direktori `htdocs/data/storage.php`.
   - **PENTING:** Jangan pernah melakukan commit file `data/storage.php` ke repositori Git publik.
5. Atur permission:
   - Folder `data/` → `755` (atau `777` jika hosting memiliki pembatasan kepemilikan file).
   - Berkas `data/storage.php` → `644` (atau `666`).
6. Verifikasi:
   - Akses `https://pojokberkah.online/` harus menampilkan beranda lengkap dengan SSR.
   - Akses langsung `https://pojokberkah.online/data/storage.php` harus menghasilkan halaman **404 Not Found**.

---

## 4. Panduan Operasional CMS Admin (Zero-JavaScript)

Panel admin diakses melalui `admin.php`. Panel ini sepenuhnya bekerja secara server-side tanpa JavaScript melalui parameter query string `admin.php?tab={nama_tab}`:

### Tab 1 — WhatsApp (`?tab=whatsapp`)
- Digunakan untuk mengubah nomor WhatsApp utama tujuan konsultasi/pertanyaan.
- Format input: 8–15 digit angka (angka awalan `08...` otomatis dikonversi menjadi `628...`).
- Nomor ini langsung disuntikkan ke seluruh tautan tombol konsultasi publik.

### Tab 2 — Logo Situs (`?tab=logo`)
- Digunakan untuk mengunggah logo resmi Pojok Berkah.
- Format yang didukung: JPG, PNG, WEBP (maksimal 5 MB).
- Logo disimpan secara inline base64 data-URI di `storage.php` dan otomatis tampil pada header seluruh halaman serta favicon.

### Tab 3 — FAQ (`?tab=faq`)
- Mengelola daftar pertanyaan yang sering diajukan pada landing page.
- Mendukung 1 hingga 20 pertanyaan (Pertanyaan maks 160 karakter, Jawaban maks 2000 karakter).
- Tombol **"Tambah baris"** menambah slot formulir baru.
- Checkbox **"Hapus saat disimpan"** menghapus pertanyaan terpilih saat form disubmit.
- Perubahan FAQ otomatis memperbarui teks di halaman beranda serta skema JSON-LD `FAQPage` di tag `<head>`.

### Tab 4 — Manajemen & Impor Artikel (`?tab=artikel`)
- **Tulis Baru / Edit Artikel:**
  - Judul: 5–120 karakter.
  - Slug: 3–60 karakter (otomatis diformat menjadi huruf kecil & tanda hubung).
  - Ringkasan (*Excerpt*): 20–320 karakter (digunakan untuk cuplikan beranda dan meta deskripsi).
  - Konten: 100–30.000 karakter (mendukung paragraf HTML).
  - Gambar Unggulan: Upload gambar JPG/PNG/WEBP (maks 2 MB) + teks alternatif (*alt text*) wajib.
  - Status: `draft` (hanya terlihat di admin) atau `published` (langsung tayang di `/artikel/`, sitemap, dan beranda).
- **Fitur Impor Artikel:**
  - Mendukung pembacaan berkas artikel lokal `.html`, `.md`, atau `.txt` di server (maks 2 MB).
  - Parser server-side otomatis mengekstrak Judul, Ringkasan, dan Konten ke dalam formulir untuk ditinjau sebelum disimpan.

### Tab 5 — Ubah Kata Sandi (`?tab=password`)
- Mengubah kata sandi administrator (memerlukan kata sandi lama dan kata sandi baru yang valid).

### Tab Arsip (Layanan, Statistik, Gambar Karya)
- Tab `?tab=layanan`, `?tab=statistik`, dan `?tab=gambar` diberi badge arsip abu-abu.
- Digunakan untuk konten pendukung pada halaman profil `tentang-kami.html` dan halaman error `404.html`.

---

## 5. Dokumentasi API Backend (`admin.php`)

Format: JSON untuk pembacaan publik, `multipart/form-data` atau `application/x-www-form-urlencoded` untuk mutasi POST dengan proteksi CSRF.

### 1. `GET admin.php?public=1` (Data Publik)
Mengembalikan seluruh pengaturan publik dalam format JSON:
```json
{
  "wa_number": "628159501935",
  "faq": [
    {
      "question": "Apa itu Pojok Berkah?",
      "answer": "Pojok Berkah adalah ruang usaha dan media informasi..."
    }
  ],
  "articles": [
    {
      "slug": "pengenalan-parkir-kamera",
      "title": "Parkir Kamera Otomatis: Pengenalan Sistem Parkir",
      "excerpt": "Kenali solusi sistem parkir terpadu berbasis kamera...",
      "updated_at": "2026-10-17",
      "image_url": "admin.php?article_image=pengenalan-parkir-kamera&v=1729180000",
      "image_alt": "Cover sistem parkir kamera"
    }
  ],
  "images": {
    "logo": "data:image/png;base64,..."
  }
}
```
*Header Response:* `Content-Type: application/json; charset=utf-8`, `Cache-Control: no-store`.

### 2. `GET admin.php?article_image={slug}` (Featured Image Artikel)
- Menyajikan data biner gambar cover artikel terbit.
- Parameter: `slug` artikel (hanya artikel berstatus `published`).
- Parameter cache buster: `&v={timestamp}` agar browser memperbarui cache saat gambar diganti.
- *Header Response:* `Cache-Control: public, max-age=86400`, `Content-Type: image/...`, `X-Content-Type-Options: nosniff`.

### 3. Aksi Mutasi Form `POST admin.php`
Seluruh aksi `POST` mewajibkan token `csrf_token` yang valid di session:

| Action (`action`) | Parameter Utama | Deskripsi |
|---|---|---|
| `login` | `password` | Autentikasi session admin |
| `save_whatsapp` | `wa_number` | Memperbarui nomor WhatsApp publik |
| `upload_logo` | `logo` (file) | Mengunggah gambar logo situs |
| `remove_logo` | - | Menghapus logo kustom |
| `save_faq` | `questions[]`, `answers[]`, `delete_indices[]` | Menyimpan array pertanyaan & jawaban FAQ |
| `save_artikel` | `title`, `slug`, `excerpt`, `content`, `status`, `article_image`, `image_alt` | Menyimpan artikel baru atau update artikel yang ada |
| `delete_artikel`| `slug` | Menghapus artikel permanen |
| `import_artikel`| `import_path` | Mengekstrak isi file server ke form edit artikel |
| `change_password`| `current_password`, `new_password`, `confirm_password` | Mengubah password admin |
| `logout` | - | Mengakhiri session admin secara aman |
