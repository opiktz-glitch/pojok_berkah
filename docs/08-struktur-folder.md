# Struktur Folder — Pojok Berkah

> Root sengaja dibuat ramping: hanya halaman + `admin.php` + config.
> Aset dikelompokkan di `assets/`, data CMS di `data/`, file tak terpakai di `_arsip/`.

```
pojok_berkah/
├── index.html                  # Beranda (tetap di root — URL utama)
├── tentang-kami.html           # Halaman profil
├── kebijakan-privasi.html      # Halaman legal
├── syarat-ketentuan.html       # Halaman legal
├── 404.html                    # Halaman error (ErrorDocument di .htaccess)
├── admin.php                   # CMS (tetap di root — fetch("admin.php?public=1"))
├── README.md                   # Panduan proyek (tetap di root)
│
├── docs/                       # DOKUMEN PANDUAN & INFORMASI (tetap, bukan arsip)
│   ├── README.md               # indeks dokumentasi
│   ├── 01-panduan-pengguna.md .. 07-log-progres.md
│   └── 08-struktur-folder.md   # file ini
│
├── assets/                     # SEMUA aset tampilan
│   ├── css/
│   │   └── legal.css           # dipakai 404 + 2 halaman legal
│   ├── icons/
│   │   ├── favicon-32x32.png
│   │   ├── favicon.ico         # + redirect /favicon.ico -> sini
│   │   └── apple-touch-icon.png
│   └── img/
│       └── og-image.jpg        # OG/Twitter/JSON-LD image
│
├── data/                       # CMS storage (diblokir .htaccess -> 404)
│   ├── storage.php             # settings live (git-ignored)
│   ├── storage.example.php     # template
│   └── uploads/                # gambar CMS + index.php anti-listing
│
├── _arsip/                     # HANYA file tak terpakai (diblokir .htaccess + robots + gitignore)
│   ├── README.md             # daftar isi arsip + cara kembalikan
│   ├── make_icons.py           # generator icon lokal (sudah tidak dibutuhkan)
│   └── favicon-16x16.png, icon-192.png, icon-512.png  # tak direferensikan
│
│   Catatan: `.github/workflows/deploy.yml` sempat diarsipkan lalu DIKEMBALIKAN
│   16 Okt 2026 agar auto-deploy FTP tetap jalan — jangan arsipkan lagi.
│
├── .htaccess                   # HTTPS + redirect www + blokir data/_arsip + redirect aset lama
├── robots.txt                  # Allow / + Disallow data, admin, _arsip
└── sitemap.xml                 # 4 URL utama
```

## Aturan bila nambah file

| Jenis | Taruh di | Contoh |
|---|---|---|
| CSS baru | `assets/css/` | `assets/css/tentang.css` |
| Icon/favicon baru | `assets/icons/` | `assets/icons/icon-192.png` |
| Gambar/OG baru | `assets/img/` | `assets/img/hero.webp` |
| Halaman baru | root | `kontak.html` |
| Ragu / tak terpakai | `_arsip/` | — |

Redirect lama -> baru sudah dipasang di `.htaccess`, jadi bookmark/SEO lama
(`/favicon.ico`, `/og-image.jpg`, `/legal.css`) tetap jalan.
