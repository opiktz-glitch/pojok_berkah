# Rekomendasi Landing Profesional — Opsi A (Hapus Total Section Produk)

> Status: REKOMENDASI / RENCANA — belum dieksekusi ke `index.html`.
> Diputuskan: 15 Oktober 2026. Landing page tetap seperti semula sampai ada perintah eksekusi.
> Konsep: landing berubah dari "direktori produk" menjadi "profil korporat Pojok Berkah".
> Tryout / Gadai / Alfi Kitchen tetap hidup di subdomain masing-masing, tidak ditampilkan di landing.

## 1. Kondisi saat ini (temuan audit)

- Nav: `Layanan (#pilih)` + `Kenapa (#kenapa)` + `FAQ (#faq)` + tombol WA.
- Hero + 3 kartu besar Tryout / Gadai / Kitchen + host subdomain + tags + stats `3 layanan / 29 cabang / Always Fresh`.
- FAQ 4 item semuanya tentang produk.
- Footer berisi link ke 3 subdomain.
- JS `fetch(admin.php?public=1)` menimpa `data-service-link/name/description/tags`, stats, dan FAQ.

## 2. Opsi A yang dipilih: hapus total dari frontend

- Hapus permanen section kartu `#pilih` + `.cards` (index.html baris 192-222) beserta atribut `data-service-*`.
- Hapus/tulis ulang stats produk (baris 224-228).
- Hapus host subdomain dari tampilan dan footer.
- Tulis ulang hero, FAQ, dan footer menjadi brand umum (detail di bawah).
- Sifat: tidak mudah dikembalikan via toggle (berbeda dengan Opsi B sembunyikan via CSS/JS). Rollback dilakukan via Git revert.
- CMS (`admin.php`) TIDAK disederhanakan dulu: field `service_urls / service_names / service_descriptions / service_tags / stats / images` tetap disimpan di `data/storage.php` dan API `?public=1` agar subdomain tidak rusak. Frontend landing hanya berhenti membacanya (Opsi A backend: frontend abaikan, backend tetap simpan).

## 3. Struktur menu profesional baru

```
[Logo Pojok Berkah]  Beranda | Tentang | Keunggulan | FAQ | Kontak  [Hubungi Kami]
```

| Menu | Target | Keterangan |
|------|--------|------------|
| Beranda | `#beranda` (hero, tambahkan id) | - |
| Tentang | `tentang-kami.html` | halaman sudah ada, tinggal selaraskan isinya |
| Keunggulan | `#keunggulan` (ganti nama dari `#kenapa`) | - |
| FAQ | `#faq` | isi ditulis ulang (umum, bukan produk) |
| Kontak | `#kontak` (tambahkan id pada section kontak) | - |
| Hubungi Kami | `https://wa.me/<nomor CMS>` | satu-satunya tombol hijau, gaya `.wa-button` |

Mobile (<700px): hanya tampil Logo + tombol WA + hamburger menu. Tambah shadow header saat scroll via 5 baris JS.

## 4. Daftar hapus / ubah di index.html

1. Hapus `.cards` + `#pilih` (3 kartu Tryout/Gadai/Kitchen).
2. Hapus/tulis ulang `.stats` produk menjadi stats korporat netral atau hapus sementara bila belum ada data jujur.
3. Hero baru (usulan):
   - Judul: `Pojok Berkah — Ruang Tumbuh untuk Belajar dan Usaha`
   - Sub: `Kami mengelola beberapa inisiatif layanan melalui unit-unit khusus dengan standar yang sama: mudah dihubungi, jelas, dan bertanggung jawab.`
   - CTA: `[Kenali Kami ↓]` primer + `[Hubungi Kami]` ghost WA. Hapus tombol `Mulai tryout / Hitung simulasi`.
4. Section pengganti agar tidak kosong (urutan):
   1. `#beranda` hero korporat
   2. `#tentang-singkat` 2-3 kalimat + tombol `Baca Tentang Kami →`
   3. `#keunggulan` 3 kolom yang ada, tulis ulang bahasanya jadi korporat
   4. Testimoni (baru, 3 kartu statis) — opsional tapi disarankan
   5. `#faq` umum baru (contoh: Apa itu Pojok Berkah? / Bagaimana menghubungi admin? / Apakah data saya aman? / Di mana info layanan terbaru?)
   6. `#kontak` CTA WA + email `admin@pojokberkah.online`
5. Footer baru: `Beranda | Tentang kami | Kebijakan Privasi | Syarat & Ketentuan | Email admin` (hapus 3 link subdomain).
6. SEO: ganti `<title>`, `meta description`, `og:*` dari `Tryout TKA, Gadai BPKB, dan Alfi Kitchen` menjadi brand umum. Hapus JSON-LD `FAQPage` produk-spesifik, sisakan `Organization` + `WebSite`.
7. JS: hapus blok `service_urls / service_names / service_descriptions / service_tags` dari fetch, sisakan `wa_number`, `images.logo`, `faq`, `stats` (bila masih dipakai).
8. CSS yang bisa dihapus setelah eksekusi: `.cards`, `.card`, `.c-tryout/.c-gadai/.c-kitchen`, `.ico`, `.card-image`, `.host`, `.chips`, `.go` bila tidak dipakai lagi.
9. `tentang-kami.html` baris 160-166 juga masih menampilkan 3 produk — harus diselaraskan agar konsisten (tahap terpisah).
10. Tidak perlu ubah: `sitemap.xml`, `robots.txt`, `.htaccess`, workflow deploy.

## 5. Dampak CMS & API

- Tidak ada perubahan skema `data/storage.php`.
- Endpoint `GET admin.php?public=1` dan `GET admin.php?image=` tetap sama.
- Panel CMS Nama/URL/Deskripsi/Tag/Gambar layanan tetap ada tapi tidak dirender landing (disimpan untuk subdomain / pemakaian ulang).
- Penyederhanaan CMS (sembunyikan panel layanan) adalah tahap terpisah, bukan bagian Opsi A ini.

## 6. Kriteria selesai (saat nanti dieksekusi)

- [ ] Tidak ada nama/link Tryout, Gadai, Alfi Kitchen di `index.html` (teks, href, maupun host)
- [ ] Menu = Beranda | Tentang | Keunggulan | FAQ | Kontak + Hubungi Kami
- [ ] Hero, stats, FAQ, footer sudah brand umum
- [ ] Tidak ada error console dari fetch publik
- [ ] `tentang-kami.html` selaras (tidak menonjolkan produk)
- [ ] Rollback jelas via Git bila ingin kembalikan
