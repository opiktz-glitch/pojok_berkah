# Panduan Kontribusi — Pojok Berkah

## 1. Alur kerja Git

- Branch utama: `main` (auto-deploy ke produksi bila variable aktif).
- Untuk perubahan: buat branch `fitur/...` atau `fix/...` dari `main`, 1 PR = 1 topik, minta review sebelum merge.
- Jangan commit: `data/storage.php`, `data/uploads/*` (kecuali `index.php`), kredensial FTP, `.ftp-deploy-sync-state.json`. Sudah diatur di `.gitignore`.

## 2. Konvensi kode

- PHP: `declare(strict_types=1)`, fungsi kecil + validasi di server, escape output via `escapeHtml()`, pesan flash Bahasa Indonesia.
- HTML: `lang="id"`, jaga `canonical`, meta OG, dan JSON-LD bila tambah halaman; daftarkan halaman baru di `sitemap.xml` + footer nav.
- JS publik: tanpa framework, pakai pola `fetch("admin.php?public=1")` + guard `typeof === "string"` seperti di `index.html`.
- CSS: variabel di `:root` (`--teal #0B8277`, dll), mobile-first breakpoint 700px/460px/420px.

## 3. Menambah slot layanan / field CMS

1. Tambah default di `admin/index.php` dan/atau `admin/actions/save_service.php` + fallback di JS `index.html`/`tentang-kami.html`.
2. Tambah validasi + action POST baru di `admin/actions/` (ikuti pola `save_*` + CSRF + cek `$isAuthenticated`).
3. Buat view baru di `admin/views/` bila diperlukan.
4. Update `docs/02-dokumentasi-api.md` dan `docs/03-spesifikasi-teknis.md`.

## 4. Testing lokal (wajib sebelum PR)

1. `copy data\storage.example.php data\storage.php` bila belum ada.
2. Buka `admin.php` → setup password → uji login salah 5x (harus lock), login benar, tiap form save, upload tiap slot (jpg/png/webp ≤5MB + coba file invalid harus ditolak), hapus gambar, ganti password, logout.
3. Buka `index.html`, `tentang-kami.html`, legal pages → pastikan fetch publik menimpa konten tanpa error console.
4. Cek langsung `/data/storage.php` harus 404 (via Apache, bukan `file://`).

## 5. Deploy otomatis

Workflow: `.github/workflows/deploy.yml` (FTPS eksplisit `ftpupload.net:21`, `server-dir: htdocs/`).

- Secrets (nama pendek menang): `FTP_HOST`/`INFINITYFREE_FTP_SERVER`, `FTP_USER`/`INFINITYFREE_FTP_USERNAME`, `FTP_PASS`/`INFINITYFREE_FTP_PASSWORD`, opsional `INFINITYFREE_FTP_SERVER_DIR`.
- Variable: `POJOK_BERKAH_DEPLOY_ENABLED=true` untuk mengaktifkan push-to-deploy.
- Langkah aman: Run workflow `dry_run=true` → cek log → set variable `true` → run `dry_run=false` sekali → push berikutnya otomatis.
- Exclude deploy: `.git*`, `node_modules`, `.github`, `README.md`, `data/storage*.php`, `data/uploads` gambar. Backup produksi manual sebelum deploy besar.

## 6. Checklist PR

- [ ] Tidak ada secret / storage produksi ikut commit
- [ ] Validasi server + pesan error Indonesia
- [ ] Halaman publik tetap jalan bila storage kosong (fallback default)
- [ ] Sitemap + navigasi diperbarui bila tambah halaman
- [ ] Docs diperbarui bila endpoint/skema berubah
